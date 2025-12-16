<?php
include 'config/connexion.php';
try {
    $stmt = $connexion->query("SELECT label, link FROM menu_items");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['label'] . " -> " . $row['link'] . "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
