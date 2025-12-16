<?php
include 'config/connexion.php';

try {
    $sql = "SELECT * FROM menu_items";
    $req = $connexion->query($sql);
    $items = $req->fetchAll(PDO::FETCH_ASSOC);
    echo "Menu Items:\n";
    print_r($items);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
