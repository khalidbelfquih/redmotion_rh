<?php
session_start();
include '../model/function.php';

// Vérifie que les données requises sont présentes
if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['role'])) {
    
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hachage MD5 du mot de passe
    $role = $_POST['role'];
    
    // Vérifier si l'email existe déjà
    $check_sql = "SELECT COUNT(*) FROM utilisateur WHERE email = ?";
    $check_req = $connexion->prepare($check_sql);
    $check_req->execute(array($email));
    $email_exists = $check_req->fetchColumn();
    
    if ($email_exists) {
        $_SESSION['message']['text'] = "Cet email est déjà utilisé";
        $_SESSION['message']['type'] = "danger";
    } else {
        // Ajouter l'utilisateur
        $sql = "INSERT INTO utilisateur (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $req = $connexion->prepare($sql);
        $req->execute(array($nom, $prenom, $email, $password, $role));
        
        if ($req->rowCount() != 0) {
            $_SESSION['message']['text'] = "Utilisateur ajouté avec succès";
            $_SESSION['message']['type'] = "success";
        } else {
            $_SESSION['message']['text'] = "Une erreur s'est produite lors de l'ajout";
            $_SESSION['message']['type'] = "danger";
        }
    }
} else {
    $_SESSION['message']['text'] = "Tous les champs sont obligatoires";
    $_SESSION['message']['type'] = "danger";
}

header('Location: ../vue/utilisateur.php');