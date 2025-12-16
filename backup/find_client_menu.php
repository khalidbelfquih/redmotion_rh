<?php
include 'config/connexion.php';

try {
    $sql = "SELECT * FROM menu_items WHERE label LIKE '%Client%'";
    $req = $connexion->query($sql);
    $items = $req->fetchAll(PDO::FETCH_ASSOC);
    echo "Client Menu Items:\n";
    print_r($items);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
