<?php
session_start();
include __DIR__ . '/../model/function.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../vue/login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';

    if ($action === 'update_info') {
        // Update Personal Info
        if (!empty($_POST['nom']) && !empty($_POST['prenom'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $prenom = htmlspecialchars($_POST['prenom']);
            
            $sql = "UPDATE utilisateur SET nom = ?, prenom = ? WHERE id = ?";
            $stmt = $connexion->prepare($sql);
            
            if ($stmt->execute([$nom, $prenom, $user_id])) {
                // Update Session Data
                $_SESSION['user']['nom'] = $nom;
                $_SESSION['user']['prenom'] = $prenom;
                
                $_SESSION['message']['text'] = "Informations mises à jour avec succès.";
                $_SESSION['message']['type'] = "success";
            } else {
                $_SESSION['message']['text'] = "Erreur lors de la mise à jour des informations.";
                $_SESSION['message']['type'] = "danger";
            }
        } else {
            $_SESSION['message']['text'] = "Veuillez remplir tous les champs obligatoires.";
            $_SESSION['message']['type'] = "danger";
        }
    } 
    
    elseif ($action === 'update_password') {
        // Update Password
        if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if ($new_password === $confirm_password) {
                // Using MD5 to match existing codebase (see modifUtilisateur.php)
                // Note: Consider upgrading to password_hash() in the future if possible
                $hashed_password = md5($new_password);
                
                $sql = "UPDATE utilisateur SET password = ? WHERE id = ?";
                $stmt = $connexion->prepare($sql);
                
                if ($stmt->execute([$hashed_password, $user_id])) {
                    $_SESSION['message']['text'] = "Mot de passe modifié avec succès.";
                    $_SESSION['message']['type'] = "success";
                } else {
                    $_SESSION['message']['text'] = "Erreur lors de la modification du mot de passe.";
                    $_SESSION['message']['type'] = "danger";
                }
            } else {
                $_SESSION['message']['text'] = "Les mots de passe ne correspondent pas.";
                $_SESSION['message']['type'] = "danger";
            }
        } else {
            $_SESSION['message']['text'] = "Veuillez remplir tous les champs de mot de passe.";
            $_SESSION['message']['type'] = "danger";
        }
    }
}

header('Location: ../vue/profile.php');
exit();
