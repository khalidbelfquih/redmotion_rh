<?php
include 'config/connexion.php';

try {
    $sql = "UPDATE menu_items SET is_active = 0 WHERE id = 5";
    $connexion->exec($sql);
    echo "Menu item 'Clients' disabled successfully.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
