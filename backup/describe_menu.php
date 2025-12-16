<?php
include 'config/connexion.php';

try {
    $stmt = $connexion->query("DESCRIBE menu_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo $col['Field'] . " | " . $col['Type'] . "\n";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
