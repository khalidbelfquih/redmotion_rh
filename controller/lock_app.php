<?php
session_start();
// Save current page to return to after unlock
if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    // Basic check to ensure we don't redirect to lock_screen itself or login
    $referer = basename($_SERVER['HTTP_REFERER']);
    if ($referer !== 'lock_screen.php' && $referer !== 'login.php' && $referer !== 'unlock_app.php' && $referer !== 'authentification.php') {
        $_SESSION['return_to'] = $_SERVER['HTTP_REFERER'];
    } else {
        $_SESSION['return_to'] = '../vue/dashboard.php';
    }
} else {
    $_SESSION['return_to'] = '../vue/dashboard.php';
}

$_SESSION['locked'] = true;
header('Location: ../vue/lock_screen.php');
exit();

