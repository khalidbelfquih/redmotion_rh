<?php
require_once 'config/connexion.php';

try {
    $sql = "ALTER TABLE mission 
            ADD COLUMN km_depart INT DEFAULT 0,
            ADD COLUMN km_arrivee INT DEFAULT 0,
            ADD COLUMN carburant_depart VARCHAR(50) DEFAULT NULL,
            ADD COLUMN carburant_arrivee VARCHAR(50) DEFAULT NULL";
    
    $connexion->exec($sql);
    echo "Colonnes ajoutées avec succès à la table mission.";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
