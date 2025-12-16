<?php
require_once 'config/connexion.php';

try {
    $stmt = $connexion->query("DESCRIBE suivi_absence");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Table 'suivi_absence' exists. Columns:\n";
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
