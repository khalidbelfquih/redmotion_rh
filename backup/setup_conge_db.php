<?php
include_once 'config/connexion.php';

$sql = file_get_contents('create_conge_table.sql');

try {
    $connexion->exec($sql);
    echo "Table 'conges' created successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
