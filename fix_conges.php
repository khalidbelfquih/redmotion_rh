<?php
require_once 'config/connexion.php';

try {
    echo "Starting Fix Process for 'conges' table...\n";
    
    // 1. Check all IDs
    $stmt = $connexion->query("SELECT id FROM conges ORDER BY id");
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Current IDs in conges: " . implode(", ", $ids) . "\n";
    
    // 2. Identify new ID
    // If only 0 exists, we can change it to 1.
    // If others exist, we take max + 1.
    $max_id = empty($ids) ? 0 : max($ids);
    $new_id = ($max_id == 0) ? 1 : $max_id + 1;
    
    // 3. Migrate ID 0 if exists
    if (in_array(0, $ids)) {
        echo "Found record with ID 0. Migrating to ID $new_id...\n";
        $update = $connexion->prepare("UPDATE conges SET id = ? WHERE id = 0");
        $update->execute([$new_id]);
        echo "Record migrated.\n";
    }
    
    // 4. Update Max ID for auto_increment
    $stmt = $connexion->query("SELECT MAX(id) FROM conges");
    $current_max = $stmt->fetchColumn(); 
    $next_auto = $current_max + 1;
    
    // 5. Enable AUTO_INCREMENT
    echo "Enabling AUTO_INCREMENT on 'conges.id'...\n";
    $sql = "ALTER TABLE conges MODIFY id int NOT NULL AUTO_INCREMENT";
    $connexion->exec($sql);
    
    echo "Setting AUTO_INCREMENT start value to $next_auto...\n";
    $connexion->exec("ALTER TABLE conges AUTO_INCREMENT = $next_auto");

    echo "Success! AUTO_INCREMENT enabled for 'conges'.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
