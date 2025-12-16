<?php
session_start();
include '../model/GoogleAuthenticator.php';

if (isset($_POST['verify_2fa'])) {
    if (!isset($_SESSION['2fa_pending_user'])) {
        header('Location: ../vue/login.php');
        exit();
    }

    $code = $_POST['code'];
    $secret = $_SESSION['2fa_pending_user']['secret'];

    $ga = new GoogleAuthenticator();
    $checkResult = $ga->verifyCode($secret, $code, 2); // 2 = 2*30sec tolerance

    if ($checkResult) {
        // Code valide
        $_SESSION['user'] = [
            'id' => $_SESSION['2fa_pending_user']['id'],
            'nom' => $_SESSION['2fa_pending_user']['nom'],
            'prenom' => $_SESSION['2fa_pending_user']['prenom'],
            'email' => $_SESSION['2fa_pending_user']['email'],
            'role' => $_SESSION['2fa_pending_user']['role']
        ];
        
        unset($_SESSION['2fa_pending_user']);
        
        header('Location: ../vue/dashboard.php');
        exit();
    } else {
        // Code invalide
        $_SESSION['message']['text'] = "Code 2FA incorrect";
        $_SESSION['message']['type'] = "danger";
        header('Location: ../vue/login.php?step=2fa');
        exit();
    }
} else {
    header('Location: ../vue/login.php');
    exit();
}
?>
