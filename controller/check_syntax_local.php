<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
try {
    include 'profile_controller.php'; 
    // This will redirect if successful because of header location at end. 
    // But if syntax error, it will show.
    // To prevent redirect, we can't easily.
    // But PHP -l equivalent involves CLI.
    echo "Controller included without fatal error.";
} catch (Throwable $t) {
    echo "Error: " . $t->getMessage();
}
?>
