<?php
require_once 'config/connexion.php';
$sql = "SELECT * FROM menu_items ORDER BY position ASC";
$stmt = $connexion->query($sql);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($items, JSON_PRETTY_PRINT);
?>
