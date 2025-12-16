<?php
// controller/planning_controller.php
session_start();
header('Content-Type: application/json');

require_once '../config/connexion.php';
require_once '../model/planning_functions.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_events':
        $start = $_GET['start'];
        $end = $_GET['end'];
        $showLeaves = isset($_GET['show_leaves']) ? (int)$_GET['show_leaves'] : 1;
        $showPresences = isset($_GET['show_presences']) ? (int)$_GET['show_presences'] : 1;
        
        // Fetch Event Types for Colors
        $stmtTypes = $connexion->query("SELECT * FROM event_types");
        $eventTypes = [];
        while($row = $stmtTypes->fetch(PDO::FETCH_ASSOC)) {
            // Index by name (lowercase) to match ENUM values from events table
            $eventTypes[strtolower($row['nom'])] = $row;
        }

        // Fetch Company Events
        $events = getEvents($start, $end);
        $calendarEvents = [];
        
        foreach ($events as $event) {
            $color = '#3788d8'; // Default blue
            
            // Lookup color by name directly
            $typeKey = strtolower($event['type']);
            if (isset($eventTypes[$typeKey])) {
                $color = $eventTypes[$typeKey]['couleur'];
            } 
            
            $calendarEvents[] = [
                'id' => 'evt_' . $event['id'],
                'title' => $event['titre'],
                'start' => $event['date_debut'],
                'end' => $event['date_fin'],
                'description' => $event['description'],
                'type' => $event['type'],
                'color' => $color,
                'extendedProps' => ['type' => 'event', 'db_id' => $event['id']]
            ];
        }
        
        // Fetch Approved Leaves
        if ($showLeaves) {
            $leaves = getApprovedLeavesForCalendar($start, $end);
            foreach ($leaves as $leave) {
                $calendarEvents[] = [
                    'id' => 'leave_' . $leave['id'],
                    'title' => 'Congé: ' . $leave['nom'] . ' ' . substr($leave['prenom'], 0, 1) . '.',
                    'start' => $leave['date_debut'],
                    'end' => date('Y-m-d', strtotime($leave['date_fin'] . ' +1 day')), // FullCalendar exclusive end date
                    'color' => '#ffc107', // Warning/Yellow
                    'textColor' => '#000',
                    'extendedProps' => ['type' => 'leave']
                ];
            }
        }

        // Fetch Presences
        if ($showPresences) {
            $presences = getPresences($start, $end);
            foreach ($presences as $presence) {
                $calendarEvents[] = [
                    'id' => 'presence_' . $presence['id'],
                    'title' => 'Présence: ' . $presence['nom'] . ' ' . substr($presence['prenom'], 0, 1) . '.',
                    'start' => $presence['date_planning'] . 'T' . $presence['heure_debut'],
                    'end' => $presence['date_planning'] . 'T' . $presence['heure_fin'],
                    'color' => '#20c997', // Teal
                    'extendedProps' => ['type' => 'presence', 'db_id' => $presence['id']]
                ];
            }
        }
        
        echo json_encode($calendarEvents);
        break;

    case 'get_today_leaves':
        $today = date('Y-m-d');
        $leaves = getApprovedLeavesForCalendar($today, $today);
        $result = [];
        foreach ($leaves as $leave) {
            $result[] = [
                'nom' => $leave['nom'] . ' ' . $leave['prenom'],
                'date_fin' => $leave['date_fin'],
                'initials' => strtoupper(substr($leave['nom'], 0, 1) . substr($leave['prenom'], 0, 1))
            ];
        }
        echo json_encode($result);
        break;

    case 'add_event':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'titre' => $_POST['titre'],
                    'description' => $_POST['description'],
                    'date_debut' => $_POST['date_debut'],
                    'date_fin' => $_POST['date_fin'],
                    'type' => $_POST['type']
                ];
                if (addEvent($data)) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("Erreur lors de l'insertion en base de données");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        break;

    case 'add_presence':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'id_employe' => $_POST['id_employe'],
                    'date_planning' => $_POST['date_planning'],
                    'heure_debut' => $_POST['heure_debut'],
                    'heure_fin' => $_POST['heure_fin']
                ];
                if (addPresence($data)) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("Erreur lors de l'ajout de la présence");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        break;

    case 'delete_presence':
        if (isset($_POST['id'])) {
            if (deletePresence($_POST['id'])) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erreur de suppression']);
            }
        }
        break;
        
    case 'update_event':
        // Implementation for drag & drop or resize could go here
        break;

    case 'delete_event':
        if (isset($_POST['id'])) {
            if (deleteEvent($_POST['id'])) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erreur de suppression']);
            }
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
