<?php
include 'config/connexion.php';
try {
    $stmt = $connexion->query("DESCRIBE menu_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($columns);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
