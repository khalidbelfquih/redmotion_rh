<?php
include 'config/connexion.php';

$sql = file_get_contents('update_voiture_schema.sql');

try {
    $connexion->exec($sql);
    echo "Mise à jour de la table voiture réussie !";
} catch (PDOException $e) {
    echo "Erreur lors de la mise à jour : " . $e->getMessage();
}
?>
