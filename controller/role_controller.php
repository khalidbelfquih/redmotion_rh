<?php
session_start();
include '../config/connexion.php';
include '../model/role_functions.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../vue/dashboard.php');
    exit();
}

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    }

    if ($action === 'add') {
        $nom = trim($_POST['nom']);
        $description = trim($_POST['description']);

        if (!empty($nom)) {
            if (ajouterRole($nom, $description)) {
                $_SESSION['message'] = ['text' => "Rôle ajouté avec succès", 'type' => 'success'];
            } else {
                $_SESSION['message'] = ['text' => "Erreur lors de l'ajout du rôle", 'type' => 'danger'];
            }
        } else {
            $_SESSION['message'] = ['text' => "Le nom du rôle est obligatoire", 'type' => 'danger'];
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $nom = trim($_POST['nom']);
        $description = trim($_POST['description']);

        if (!empty($nom) && !empty($id)) {
            if (modifierRole($id, $nom, $description)) {
                $_SESSION['message'] = ['text' => "Rôle modifié avec succès", 'type' => 'success'];
            } else {
                $_SESSION['message'] = ['text' => "Erreur lors de la modification du rôle", 'type' => 'danger'];
            }
        }
    }
    
    header('Location: ../vue/roles.php');
    exit();
}

if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = supprimerRole($id);
    
    if ($result === true) {
        $_SESSION['message'] = ['text' => "Rôle supprimé avec succès", 'type' => 'success'];
    } elseif ($result === 'assigned') {
        $_SESSION['message'] = ['text' => "Impossible de supprimer ce rôle car il est assigné à des utilisateurs", 'type' => 'warning'];
    } else {
        $_SESSION['message'] = ['text' => "Erreur lors de la suppression du rôle", 'type' => 'danger'];
    }
    
    header('Location: ../vue/roles.php');
    exit();
}
?>
