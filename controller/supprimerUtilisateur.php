<?php
session_start();
require_once '../config/connexion.php'; // Inclure la connexion à la base de données

if (!empty($_GET['id'])) {
    // Vérifier que l'utilisateur ne se supprime pas lui-même
    if ($_GET['id'] == $_SESSION['user']['id']) {
        $_SESSION['message']['text'] = "Vous ne pouvez pas supprimer votre propre compte";
        $_SESSION['message']['type'] = "danger";
    } else {
        $sql = "DELETE FROM utilisateur WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute(array($_GET['id']));
        
        if ($req->rowCount() != 0) {
            $_SESSION['message']['text'] = "Utilisateur supprimé avec succès";
            $_SESSION['message']['type'] = "success";
        } else {
            $_SESSION['message']['text'] = "Une erreur s'est produite lors de la suppression";
            $_SESSION['message']['type'] = "danger";
        }
    }
} else {
    $_SESSION['message']['text'] = "ID utilisateur non spécifié";
    $_SESSION['message']['type'] = "danger";
}

header('Location: ../vue/utilisateur.php');