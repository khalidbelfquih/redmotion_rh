<?php
// controller/ajax_payroll.php
session_start();
header('Content-Type: application/json');

// Include database connection and functions
require_once '../config/connexion.php';
require_once '../model/payroll_functions.php';

// Check authentication (optional but recommended)
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'get_history' && isset($_GET['id_employe'])) {
    try {
        $id_employe = $_GET['id_employe'];
        $history = getDetailedPaymentHistory($id_employe);
        echo json_encode($history);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} elseif (isset($_GET['action']) && $_GET['action'] == 'get_lateness_stats' && isset($_GET['id_employe'])) {
    try {
        require_once '../model/retard_functions.php';
        require_once '../model/heures_sup_functions.php';

        $id_employe = $_GET['id_employe'];
        $mois = $_GET['mois'] ?? date('n');
        $annee = $_GET['annee'] ?? date('Y');
        
        $stats_retard = getTotalRetardMinutes($id_employe, $mois, $annee);
        $stats_heures_sup = getTotalHeuresSupMinutes($id_employe, $mois, $annee);

        echo json_encode([
            'total_minutes_retard' => $stats_retard['total_minutes'] ?? 0,
            'nb_retards' => $stats_retard['nb_retards'] ?? 0,
            'total_minutes_sup' => $stats_heures_sup['total_minutes'] ?? 0,
            'nb_heures_sup' => $stats_heures_sup['nb_heures_sup'] ?? 0
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètres invalides']);
}
?>
