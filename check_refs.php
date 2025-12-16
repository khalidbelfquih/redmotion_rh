<?php
require_once 'config/connexion.php';

$tables = [
    'conges', 'documents_employe', 'employe_documents', 'heures_sup', 
    'paiements', 'paiements_salaire', 'planning_presence', 'primes', 
    'retards', 'suivi_absence'
];

echo "Checking for references to employee ID 0...\n";
$found = false;
foreach ($tables as $table) {
    try {
        $stmt = $connexion->query("SELECT COUNT(*) as count FROM $table WHERE id_employe = 0");
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            echo "Found $count records in table '$table'.\n";
            $found = true;
        }
    } catch (PDOException $e) {
        // Some tables might not exist or use different column name?
        // But from SQL dump, they all use id_employe.
        echo "Error checking $table: " . $e->getMessage() . "\n";
    }
}

if (!$found) {
    echo "No references found. Safe to update ID.\n";
} else {
    echo "References found. Need to update child tables too.\n";
}
?>
