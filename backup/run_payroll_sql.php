<?php
include 'config/connexion.php';

$sql = file_get_contents('create_payroll_tables.sql');

try {
    $connexion->exec($sql);
    echo "Tables Paie créées avec succès.";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
