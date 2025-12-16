<?php
include 'function.php';

try {
    $connexion = getPDO();
    
    // Create event_types table
    $sql = "CREATE TABLE IF NOT EXISTS event_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        couleur VARCHAR(20) DEFAULT '#E63946',
        icon VARCHAR(50) DEFAULT 'bx-calendar-event'
    )";
    $connexion->exec($sql);
    
    // Insert default types if empty
    $stmt = $connexion->query("SELECT COUNT(*) FROM event_types");
    if ($stmt->fetchColumn() == 0) {
        $defaults = [
            ['Réunion', '#3B82F6', 'bx-briefcase'],
            ['Événement', '#F59E0B', 'bx-party'],
            ['Formation', '#10B981', 'bx-book-open'],
            ['Autre', '#6B7280', 'bx-pin']
        ];
        
        $insert = $connexion->prepare("INSERT INTO event_types (nom, couleur, icon) VALUES (?, ?, ?)");
        foreach ($defaults as $type) {
            $insert->execute($type);
        }
        echo "Table event_types created and defaults inserted.";
    } else {
        echo "Table already exists and has data.";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
