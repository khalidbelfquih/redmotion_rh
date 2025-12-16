<?php
include 'config/connexion.php';

try {
    $sql = file_get_contents('add_hr_menu.sql');
    $connexion->exec($sql);
    echo "HR menu items added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit(1);
}
?>
