<?php
require_once 'config/connexion.php';

try {
    $sql = file_get_contents('create_planning_presence_table.sql');
    $connexion->exec($sql);
    echo "Table 'planning_presence' created successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
