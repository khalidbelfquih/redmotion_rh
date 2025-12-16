<?php
include 'config/connexion.php';

$sql = file_get_contents('add_payroll_menu.sql');

try {
    $connexion->exec($sql);
    echo "Menu Finance & Paie ajouté avec succès.";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
