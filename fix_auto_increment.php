<?php
require_once 'config/connexion.php';

$tables_with_id_employe = [
    'conges', 'deductions', 'documents_employe', 'employe_documents', 'heures_sup', 
    'paiements', 'paiements_salaire', 'planning_presence', 'primes', 
    'retards', 'suivi_absence'
];

try {
    echo "Starting Fix Process...\n";
    
    // 1. Disable FK Checks
    $connexion->exec("SET FOREIGN_KEY_CHECKS=0");
    echo "Foreign key checks disabled.\n";

    // 2. Identify max ID
    $stmt = $connexion->query("SELECT MAX(id) as max_id FROM employes");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $max_id = $result['max_id'] ? $result['max_id'] : 0;
    $new_id = $max_id + 1;
    
    // 3. Check if ID 0 exists
    $stmt0 = $connexion->query("SELECT COUNT(*) FROM employes WHERE id=0");
    if ($stmt0->fetchColumn() > 0) {
        echo "Found employee with ID 0. Migrating to ID $new_id...\n";
        
        // Update main table
        $updateMain = $connexion->prepare("UPDATE employes SET id = ? WHERE id = 0");
        $updateMain->execute([$new_id]);
        echo "Updated employes table.\n";
        
        // Update references
        foreach ($tables_with_id_employe as $table) {
            try {
                // Check if table has records with id_employe=0
                // (Optimization: blind update is also fine but logging is nice)
                $updateRef = $connexion->prepare("UPDATE $table SET id_employe = ? WHERE id_employe = 0");
                $updateRef->execute([$new_id]);
                $count = $updateRef->rowCount();
                if ($count > 0) {
                    echo "Updated $count records in $table.\n";
                }
            } catch (Exception $e) {
                // Table might not exist or column missing
                echo "Skipped $table (or error: " . $e->getMessage() . ")\n";
            }
        }
    } else {
        echo "No employee with ID 0 found. Proceeding with AUTO_INCREMENT fix only.\n";
        // If no ID 0, next_id is definitely max+1
    }
    
    // 4. Enable AUTO_INCREMENT
    // New max ID might be $new_id now.
    $stmt = $connexion->query("SELECT MAX(id) as max_id FROM employes");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_max = $result['max_id'];
    $next_auto = $current_max + 1;

    echo "Enabling AUTO_INCREMENT on 'employes.id'...\n";
    $connexion->exec("ALTER TABLE employes MODIFY id int NOT NULL AUTO_INCREMENT");
    
    echo "Setting AUTO_INCREMENT start value to $next_auto...\n";
    $connexion->exec("ALTER TABLE employes AUTO_INCREMENT = $next_auto");

    echo "Success! AUTO_INCREMENT enabled.\n";

    // 5. Enable FK Checks
    $connexion->exec("SET FOREIGN_KEY_CHECKS=1");
    echo "Foreign key checks re-enabled.\n";
    
    echo "\nVerification (SHOW CREATE TABLE):\n";
    $stmt = $connexion->query("SHOW CREATE TABLE employes");
    print_r($stmt->fetch(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    $connexion->exec("SET FOREIGN_KEY_CHECKS=1"); // Try to re-enable
}
?>
