<?php
require_once 'config/connexion.php';
$sql = "DESCRIBE employes";
$stmt = $connexion->query($sql);
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($columns, JSON_PRETTY_PRINT);
?>
