<?php
include '../config/connexion.php';

try {
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Create event_types table
    $sqlTypes = "CREATE TABLE IF NOT EXISTS event_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(50) NOT NULL,
        couleur VARCHAR(20) NOT NULL
    )";
    $connexion->exec($sqlTypes);
    
    // Reset and Populate event types to match ENUM
    $connexion->exec("TRUNCATE TABLE event_types");
    
    $defaults = [
        ['nom' => 'reunion', 'couleur' => '#3B82F6'], // Blue
        ['nom' => 'evenement', 'couleur' => '#EF4444'], // Red
        ['nom' => 'autre', 'couleur' => '#6B7280'] // Gray
    ];
    
    $insert = $connexion->prepare("INSERT INTO event_types (nom, couleur) VALUES (?, ?)");
    foreach ($defaults as $type) {
        $insert->execute([$type['nom'], $type['couleur']]);
    }
    echo "Event types reset to: reunion, evenement, autre.<br>";

    // 3. Create events table with ENUM type
    $sqlEvents = "CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        date_debut DATETIME NOT NULL,
        date_fin DATETIME NOT NULL,
        type ENUM('reunion', 'evenement', 'autre') DEFAULT 'reunion', 
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $connexion->exec($sqlEvents);
    
    // Force alter to ensure ENUM is applied if table exists
    try {
        $connexion->exec("ALTER TABLE events MODIFY COLUMN type ENUM('reunion', 'evenement', 'autre') DEFAULT 'reunion'");
        echo "Table 'events' type updated to ENUM.<br>";
    } catch (PDOException $e) { }

    echo "Tables checked/created.<br>";

    // 4. Create planning_presence table                                                       
    $sqlPresence = "CREATE TABLE IF NOT EXISTS planning_presence (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_employe INT NOT NULL,
        date_planning DATE NOT NULL,
        heure_debut TIME NOT NULL,
        heure_fin TIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_employe) REFERENCES employes(id) ON DELETE CASCADE
    )";
    $connexion->exec($sqlPresence);
    echo "Table 'planning_presence' checked/created.<br>";

    echo "Database structure fixed successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
