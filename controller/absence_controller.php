<?php
// controller/absence_controller.php
session_start();

require_once '../config/connexion.php';
require_once '../model/absence_functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add_absence':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_employe' => $_POST['id_employe'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'type_absence' => $_POST['type_absence'],
                'motif' => $_POST['motif'],
                'justifie' => isset($_POST['justifie']) ? 1 : 0
            ];
            
            if (addAbsence($data)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Absence enregistrée avec succès.'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de l\'enregistrement.'];
            }
            header('Location: ../vue/retards.php?tab=absence');
        }
        break;

    case 'delete_absence':
        if (isset($_POST['id'])) {
            if (deleteAbsence($_POST['id'])) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Absence supprimée.'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de la suppression.'];
            }
            header('Location: ../vue/retards.php?tab=absence');
        }
        break;
        
    case 'update_absence':
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = [
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'type_absence' => $_POST['type_absence'],
                'motif' => $_POST['motif'],
                'justifie' => isset($_POST['justifie']) ? 1 : 0
            ];
            
            if (updateAbsence($id, $data)) {
                 $_SESSION['message'] = ['type' => 'success', 'text' => 'Absence modifiée avec succès.'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de la modification.'];
            }
            header('Location: ../vue/retards.php?tab=absence');
         }
         break;

    default:
        header('Location: ../vue/retards.php?tab=absence');
        break;
}
?>
