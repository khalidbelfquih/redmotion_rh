<?php
session_start();
include_once '../model/conge_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../vue/login.php');
    exit();
}

// Handle New Leave Request
if (isset($_POST['demande_conge'])) {
    // Handle File Upload
    $justificatif = null;
    if (isset($_FILES['justificatif']) && $_FILES['justificatif']['error'] == 0) {
        $uploadDir = '../public/uploads/justificatifs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['justificatif']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['justificatif']['tmp_name'], $targetPath)) {
            $justificatif = $fileName;
        }
    }

    $data = [
        'id_employe' => $_POST['id_employe'] ?? $_SESSION['user']['id'], 
        'type_conge' => $_POST['type_conge'],
        'date_debut' => $_POST['date_debut'],
        'date_fin' => $_POST['date_fin'],
        'motif' => $_POST['motif'],
        'justificatif' => $justificatif
    ];

    // Simple validation
    if (strtotime($data['date_fin']) < strtotime($data['date_debut'])) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'La date de fin doit être après la date de début.'];
    } elseif (checkCongeOverlap($data['id_employe'], $data['date_debut'], $data['date_fin'])) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Cet employé a déjà un congé validé ou en attente sur cette période.'];
    } else {
        if (addConge($data)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Demande de congé soumise avec succès.'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de la soumission.'];
        }
    }
    header('Location: ../vue/conges.php');
    exit();
}

// Handle Status Update (Admin)
if (isset($_POST['update_statut'])) {
    // Check admin role
    if ($_SESSION['user']['role'] !== 'admin') {
        header('Location: ../vue/conges.php');
        exit();
    }

    $id = $_POST['id_conge'];
    $statut = $_POST['statut'];
    $commentaire = $_POST['commentaire_admin'] ?? '';

    if (updateStatutConge($id, $statut, $commentaire)) {
        if ($statut === 'Approuvé') {
            // Get the employee ID for this leave
            $conge = getCongeById($id);
            if ($conge) {
                include_once '../model/hr_functions.php';
                updateStatutEmploye($conge['id_employe'], 'Congé');
            }
        }
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Statut du congé mis à jour.'];
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de la mise à jour.'];
    }
    header('Location: ../vue/conges.php');
    exit();
}

// Handle Delete Leave
if (isset($_POST['delete_conge'])) {
    $id = $_POST['id_conge'];
    // Optional: Check if user owns the leave or is admin
    if (deleteConge($id)) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Demande de congé supprimée.'];
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de la suppression.'];
    }
    header('Location: ../vue/conges.php');
    exit();
}

// Handle Update Leave
if (isset($_POST['update_conge'])) {
    // Handle File Upload
    $justificatif = null;
    if (isset($_FILES['justificatif']) && $_FILES['justificatif']['error'] == 0) {
        $uploadDir = '../public/uploads/justificatifs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['justificatif']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['justificatif']['tmp_name'], $targetPath)) {
            $justificatif = $fileName;
        }
    }

    $data = [
        'id' => $_POST['id_conge'],
        'id_employe' => $_POST['id_employe'] ?? $_SESSION['user']['id'], 
        'type_conge' => $_POST['type_conge'],
        'date_debut' => $_POST['date_debut'],
        'date_fin' => $_POST['date_fin'],
        'motif' => $_POST['motif']
    ];
    
    if ($justificatif) {
        $data['justificatif'] = $justificatif;
    }

    if (strtotime($data['date_fin']) < strtotime($data['date_debut'])) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'La date de fin doit être après la date de début.'];
    } elseif (checkCongeOverlap($data['id_employe'], $data['date_debut'], $data['date_fin'], $data['id'])) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Cet employé a déjà un congé validé ou en attente sur cette période.'];
    } else {
        if (updateConge($data)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Demande de congé mise à jour.'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de la mise à jour.'];
        }
    }
    header('Location: ../vue/conges.php');
    exit();
}

// Handle Finish Leave
if (isset($_POST['finish_conge'])) {
    // Check admin role
    if ($_SESSION['user']['role'] !== 'admin') {
        header('Location: ../vue/conges.php');
        exit();
    }

    $id = $_POST['id_conge'];
    
    // Get the employee ID for this leave
    $conge = getCongeById($id);
    if ($conge) {
        include_once '../model/hr_functions.php';
        if (updateStatutEmploye($conge['id_employe'], 'Actif')) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Employé réactivé avec succès.'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de la réactivation.'];
        }
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Congé introuvable.'];
    }
    header('Location: ../vue/conges.php');
    exit();
}
?>
