<?php
session_start();
include '../config/connexion.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../vue/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $userId = $_SESSION['user']['id'];

    if (!empty($password)) {
        try {
            $sql = "SELECT password FROM utilisateur WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute(array($userId));
            $user = $req->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verify password (MD5 as typically used in this app per authentification.php)
                if (md5($password) === $user['password']) {
                    // Success
                    unset($_SESSION['locked']);
                    unset($_SESSION['error_unlock']);
                    
                    if (isset($_SESSION['return_to']) && !empty($_SESSION['return_to'])) {
                        $returnUrl = $_SESSION['return_to'];
                        unset($_SESSION['return_to']);
                        header('Location: ' . $returnUrl);
                    } else {
                        header('Location: ../vue/dashboard.php');
                    }
                    exit();
                }
            }
        } catch (Exception $e) {
            // Error handling
        }
    }
    
    $_SESSION['error_unlock'] = "Mot de passe incorrect";
    header('Location: ../vue/lock_screen.php');
    exit();

} else {
    header('Location: ../vue/lock_screen.php');
    exit();
}
?>
