<?php
// controller/heures_sup_controller.php
session_start();
require_once '../config/connexion.php';
require_once '../model/heures_sup_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_heure_sup') {
        $data = [
            'id_employe' => $_POST['id_employe'],
            'date_heure' => $_POST['date_heure'],
            'duree_minutes' => $_POST['duree_minutes'],
            'motif' => $_POST['motif'],
            'valide' => isset($_POST['valide']) ? 1 : 0
        ];

        if (addHeureSup($data)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Heure supplémentaire ajoutée avec succès'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de l\'ajout'];
        }
    } elseif ($action === 'delete_heure_sup') {
        $id = $_POST['id'];
        if (deleteHeureSup($id)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Heure supplémentaire supprimée'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de la suppression'];
        }
    }

    header('Location: ../vue/retards.php?tab=heures_sup');
    exit;
}
?>
