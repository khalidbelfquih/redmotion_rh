<?php
include 'config/connexion.php';

try {
    $sql = "ALTER TABLE utilisateur ADD COLUMN google_2fa_secret VARCHAR(255) DEFAULT NULL";
    $connexion->exec($sql);
    echo "Column 'google_2fa_secret' added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
