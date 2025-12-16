<?php
session_start();
include '../model/function.php';

// Vérifie que l'ID et les données requises sont présents
if (!empty($_POST['id']) && !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['role'])) {
    
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    // Vérifier si l'email existe déjà pour un autre utilisateur
    $check_sql = "SELECT COUNT(*) FROM utilisateur WHERE email = ? AND id != ?";
    $check_req = $connexion->prepare($check_sql);
    $check_req->execute(array($email, $id));
    $email_exists = $check_req->fetchColumn();
    
    if ($email_exists) {
        $_SESSION['message']['text'] = "Cet email est déjà utilisé par un autre utilisateur";
        $_SESSION['message']['type'] = "danger";
    } else {
        // Si un nouveau mot de passe est fourni
        if (!empty($_POST['password'])) {
            $password = md5($_POST['password']); // Hachage MD5 du mot de passe
            $sql = "UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, password = ?, role = ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute(array($nom, $prenom, $email, $password, $role, $id));
        } else {
            // Si aucun mot de passe n'est fourni, ne pas le modifier
            $sql = "UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, role = ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute(array($nom, $prenom, $email, $role, $id));
        }
        
        if ($req->rowCount() != 0) {
            $_SESSION['message']['text'] = "Utilisateur modifié avec succès";
            $_SESSION['message']['type'] = "success";
        } else {
            $_SESSION['message']['text'] = "Aucune modification n'a été effectuée";
            $_SESSION['message']['type'] = "info";
        }
    }
} else {
    $_SESSION['message']['text'] = "Tous les champs sont obligatoires";
    $_SESSION['message']['type'] = "danger";
}

header('Location: ../vue/utilisateur.php');