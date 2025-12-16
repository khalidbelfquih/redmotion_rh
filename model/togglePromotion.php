<?php
include 'connexion.php';

if (!empty($_GET['id']) && isset($_GET['status'])) {
    try {
        $sql = "UPDATE promotion SET actif = ? WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$_GET['status'], $_GET['id']]);
        
        $_SESSION['message']['text'] = "Statut de la promotion mis à jour avec succès";
        $_SESSION['message']['type'] = "success";
    } catch (Exception $e) {
        $_SESSION['message']['text'] = "Erreur: " . $e->getMessage();
        $_SESSION['message']['type'] = "danger";
    }
} else {
    $_SESSION['message']['text'] = "Paramètres manquants";
    $_SESSION['message']['type'] = "danger";
}

header("Location: ../vue/promotion.php");
exit();