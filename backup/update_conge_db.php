<?php
include_once 'config/connexion.php';

$sql = "ALTER TABLE conges ADD COLUMN justificatif VARCHAR(255) DEFAULT NULL";

try {
    $connexion->exec($sql);
    echo "Column 'justificatif' added successfully.";
} catch (PDOException $e) {
    echo "Error updating table: " . $e->getMessage();
}
?>
