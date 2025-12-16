<?php
require_once 'config/connexion.php';
$sql = "SHOW TABLES";
$stmt = $connexion->query($sql);
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($tables, JSON_PRETTY_PRINT);
?>
