<?php
include 'config/connexion.php';
try {
    $stmt = $connexion->query("SELECT * FROM menu_items");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($items);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
