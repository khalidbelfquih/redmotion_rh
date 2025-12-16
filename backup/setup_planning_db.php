<?php
include 'config/connexion.php';

try {
    // Create events table (already done, but safe to keep)
    $sql = "CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        date_debut DATETIME NOT NULL,
        date_fin DATETIME NOT NULL,
        type ENUM('reunion', 'evenement', 'autre') DEFAULT 'autre',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $connexion->exec($sql);
    echo "Table 'events' checked.<br>";

    // Add menu item
    $checkMenu = $connexion->query("SELECT id FROM menu_items WHERE link = 'planning.php'");
    if ($checkMenu->rowCount() == 0) {
        // Use 'position' instead of 'order'
        $sqlMenu = "INSERT INTO menu_items (label, link, icon, role_required, position) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connexion->prepare($sqlMenu);
        $stmt->execute(['Planning', 'planning.php', 'bx-calendar', 'admin', 99]);
        echo "Menu item 'Planning' added successfully.<br>";
    } else {
        echo "Menu item 'Planning' already exists.<br>";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
