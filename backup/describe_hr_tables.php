<?php
require_once 'config/connexion.php';

$tables = ['paiements', 'conges', 'retards', 'candidats'];

foreach ($tables as $table) {
    echo "Table: $table\n";
    try {
        $sql = "DESCRIBE $table";
        $stmt = $connexion->query($sql);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "  " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    } catch (Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
?>
