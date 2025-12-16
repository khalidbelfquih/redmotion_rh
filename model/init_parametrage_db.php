<?php
include_once __DIR__ . '/function.php';

$pdo = getPDO();

try {
    // 1. Table Types de congé
    $sqlCongeType = "CREATE TABLE IF NOT EXISTS conge_type (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        description TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sqlCongeType);
    echo "Table 'conge_type' created or already exists.\n";

    // 2. Table Jours fériés
    $sqlJoursFeries = "CREATE TABLE IF NOT EXISTS jours_feries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        date_ferie DATE NOT NULL,
        recurrent BOOLEAN DEFAULT FALSE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sqlJoursFeries);
    echo "Table 'jours_feries' created or already exists.\n";

    // 3. Table Types de postes
    $sqlPosteType = "CREATE TABLE IF NOT EXISTS poste_type (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        description TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sqlPosteType);
    echo "Table 'poste_type' created or already exists.\n";

    // 4. Table Informations Société
    $sqlSociete = "CREATE TABLE IF NOT EXISTS societe_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        adresse TEXT,
        telephone VARCHAR(50),
        email VARCHAR(100),
        rc VARCHAR(100),
        nif VARCHAR(100),
        nis VARCHAR(100),
        art VARCHAR(100),
        logo_path VARCHAR(255),
        cachet_path VARCHAR(255)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sqlSociete);
    echo "Table 'societe_info' created or already exists.\n";
    
    // Insert default society info if empty
    $checkSociete = $pdo->query("SELECT COUNT(*) FROM societe_info")->fetchColumn();
    if ($checkSociete == 0) {
        $pdo->exec("INSERT INTO societe_info (nom, adresse, telephone, email) VALUES ('RED MOTION', 'Adresse par défaut', '0000000000', 'contact@redmotion.com')");
        echo "Default society info inserted.\n";
    }

    // 5. Add Paramétrage to menu_items if not exists
    // First check if table exists (it might be named differently based on previous context, but user mentioned menu)
    // Assuming menu_items table exists because of model/menu_functions.php
    
    // Check if 'Paramétrage' exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM menu_items WHERE label = ?");
    $stmt->execute(['Paramétrage']);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Get max position
        $maxPos = $pdo->query("SELECT MAX(position) FROM menu_items")->fetchColumn();
        $position = $maxPos ? $maxPos + 1 : 1;

        $slqMenu = "INSERT INTO menu_items (label, icon, link, role_required, position, is_active) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtMenu = $pdo->prepare($slqMenu);
        $stmtMenu->execute(['Paramétrage', 'bx bx-cog', 'parametrage.php', 'admin', $position, 1]);
        echo "Menu item 'Paramétrage' added.\n";
    } else {
        echo "Menu item 'Paramétrage' already exists.\n";
    }
    
    // Check/Create folder for uploads
    $uploadDir = __DIR__ . '/../public/uploads/societe';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        echo "Upload directory created: $uploadDir\n";
    }

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage() . "\n");
}
?>
