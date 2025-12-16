<?php
require_once 'config/connexion.php';

try {
    echo "Checking 'conges' table structure:\n";
    $stmt = $connexion->query("SHOW CREATE TABLE conges");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($row);

    echo "\nChecking Max ID:\n";
    $stmt = $connexion->query("SELECT MAX(id) FROM conges");
    echo "Max ID: " . $stmt->fetchColumn() . "\n";
    
    echo "\nChecking if ID 0 exists:\n";
    $stmt = $connexion->query("SELECT * FROM conges WHERE id=0");
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($res) {
        echo "Record with ID 0 found:\n";
        print_r($res);
    } else {
        echo "No record with ID 0 found.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
