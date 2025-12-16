<?php
include 'config/connexion.php';

try {
    $sql = "ALTER TABLE employes 
            ADD COLUMN situation_familiale VARCHAR(50) DEFAULT NULL,
            ADD COLUMN nombre_enfants INT DEFAULT 0,
            ADD COLUMN type_contrat VARCHAR(50) DEFAULT NULL,
            ADD COLUMN rib VARCHAR(50) DEFAULT NULL";

    $connexion->exec($sql);
    echo "Columns added successfully to 'employes' table.";
} catch(PDOException $e) {
    echo "Error adding columns: " . $e->getMessage();
}
?>
