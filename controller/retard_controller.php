<?php
// controller/retard_controller.php
session_start();

require_once '../config/connexion.php';
require_once '../model/retard_functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add_retard':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_employe' => $_POST['id_employe'],
                'date_retard' => $_POST['date_retard'],
                'duree_minutes' => $_POST['duree_minutes'],
                'motif' => $_POST['motif'],
                'justifie' => isset($_POST['justifie']) ? 1 : 0
            ];
            
            if (addRetard($data)) {
                // Check if employee reached 3 unjustified latenesses
                if ($data['justifie'] == 0) {
                    $mois = date('m', strtotime($data['date_retard']));
                    $annee = date('Y', strtotime($data['date_retard']));
                    $count = getUnjustifiedRetardsCount($data['id_employe'], $mois, $annee);
                    
                    if ($count >= 3) {
                        $_SESSION['message'] = ['type' => 'warning', 'text' => 'Retard ajouté. ATTENTION: Cet employé a atteint 3 retards injustifiés ce mois-ci !'];
                    } else {
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'Retard enregistré avec succès.'];
                    }
                } else {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Retard enregistré avec succès.'];
                }
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de l\'enregistrement.'];
            }
            header('Location: ../vue/retards.php');
        }
        break;

    case 'delete_retard':
        if (isset($_POST['id'])) {
            if (deleteRetard($_POST['id'])) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Retard supprimé.'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de la suppression.'];
            }
            header('Location: ../vue/retards.php');
        }
        break;
        
    case 'update_retard':
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = [
                'date_retard' => $_POST['date_retard'],
                'duree_minutes' => $_POST['duree_minutes'],
                'motif' => $_POST['motif'],
                'justifie' => isset($_POST['justifie']) ? 1 : 0
            ];
            
            if (updateRetard($id, $data)) {
                 $_SESSION['message'] = ['type' => 'success', 'text' => 'Retard modifié avec succès.'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erreur lors de la modification.'];
            }
            header('Location: ../vue/retards.php');
         }
         break;

    default:
        header('Location: ../vue/retards.php');
        break;
}
?>
