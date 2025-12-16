<?php
require_once 'config/connexion.php';

try {
    echo "Current Table Structure:\n";
    $stmt = $connexion->query("SHOW CREATE TABLE employes");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($row);
    
    echo "\nCurrent IDs:\n";
    $stmt = $connexion->query("SELECT id FROM employes ORDER BY id");
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo implode(", ", $ids) . "\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
