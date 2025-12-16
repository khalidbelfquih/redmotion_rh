<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    include 'controller/profile_controller.php';
    echo "Controller Syntax OK";
} catch (Throwable $t) {
    echo "Error: " . $t->getMessage() . " on line " . $t->getLine();
}
?>
