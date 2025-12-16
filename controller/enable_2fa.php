<?php
session_start();
include '../config/connexion.php';
include '../model/GoogleAuthenticator.php';

if (isset($_POST['enable_2fa'])) {
    if (!isset($_SESSION['user']) || !isset($_SESSION['2fa_secret_new'])) {
        header('Location: ../vue/login.php');
        exit();
    }

    $code = $_POST['code'];
    $secret = $_SESSION['2fa_secret_new'];
    $userId = $_SESSION['user']['id'];

    $ga = new GoogleAuthenticator();
    $checkResult = $ga->verifyCode($secret, $code, 2);

    if ($checkResult) {
        // Code valide, enregistrer le secret
        try {
            $sql = "UPDATE utilisateur SET google_2fa_secret = ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute(array($secret, $userId));
            
            $_SESSION['message']['text'] = "Authentification 2FA activée avec succès !";
            $_SESSION['message']['type'] = "success";
            
            unset($_SESSION['2fa_secret_new']);
            header('Location: ../vue/dashboard.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['message']['text'] = "Erreur lors de l'activation : " . $e->getMessage();
            $_SESSION['message']['type'] = "danger";
            header('Location: ../vue/setup_2fa.php');
            exit();
        }
    } else {
        $_SESSION['message']['text'] = "Code incorrect. Veuillez réessayer.";
        $_SESSION['message']['type'] = "danger";
        header('Location: ../vue/setup_2fa.php');
        exit();
    }
} else {
    header('Location: ../vue/dashboard.php');
    exit();
}
?>
