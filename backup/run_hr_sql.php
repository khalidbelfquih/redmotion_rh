<?php
include 'config/connexion.php';

try {
    $sql = file_get_contents('create_hr_tables.sql');
    $connexion->exec($sql);
    echo "HR tables created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit(1);
}
?>
