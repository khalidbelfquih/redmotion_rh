<?php
// Try to include the suspect file and print 'OK' if successful
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    include 'model/hr_functions.php';
    echo "Syntax OK";
} catch (Throwable $t) {
    echo "Error: " . $t->getMessage() . " on line " . $t->getLine();
}
?>
