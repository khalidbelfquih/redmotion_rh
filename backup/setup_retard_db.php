<?php
include 'config/connexion.php';

try {
    // Create retards table
    $sql = "CREATE TABLE IF NOT EXISTS retards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_employe INT NOT NULL,
        date_retard DATE NOT NULL,
        duree_minutes INT NOT NULL,
        motif TEXT,
        justifie TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_employe) REFERENCES employes(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $connexion->exec($sql);
    echo "Table 'retards' created/checked successfully.<br>";

    // Add menu item
    $checkMenu = $connexion->query("SELECT id FROM menu_items WHERE link = 'retards.php'");
    if ($checkMenu->rowCount() == 0) {
        $sqlMenu = "INSERT INTO menu_items (label, link, icon, role_required, position) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connexion->prepare($sqlMenu);
        $stmt->execute(['Retards', 'retards.php', 'bx-timer', 'admin', 5]); // Position 5, adjust as needed
        echo "Menu item 'Retards' added successfully.<br>";
    } else {
        echo "Menu item 'Retards' already exists.<br>";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
