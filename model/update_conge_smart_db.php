<?php
include_once __DIR__ . '/function.php';

$pdo = getPDO();

try {
    // 1. Add 'solde_conge' to 'employes'
    $stmt = $pdo->query("SHOW COLUMNS FROM employes LIKE 'solde_conge'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE employes ADD COLUMN solde_conge FLOAT DEFAULT 18.0");
        echo "Column 'solde_conge' added to 'employes'.\n";
    }

    // 2. Add 'jours_conge_annuel' and 'weekend_days' to 'societe_info'
    $stmt = $pdo->query("SHOW COLUMNS FROM societe_info LIKE 'jours_conge_annuel'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE societe_info ADD COLUMN jours_conge_annuel INT DEFAULT 18");
        echo "Column 'jours_conge_annuel' added to 'societe_info'.\n";
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM societe_info LIKE 'weekend_days'");
    if (!$stmt->fetch()) {
        // Default 0 (Sunday)
        $pdo->exec("ALTER TABLE societe_info ADD COLUMN weekend_days VARCHAR(50) DEFAULT '0'");
        echo "Column 'weekend_days' added to 'societe_info'.\n";
    }
    
    // Ensure 'societe_info' has a row
    $stmt = $pdo->query("SELECT id FROM societe_info LIMIT 1");
    if (!$stmt->fetch()) {
         $pdo->exec("INSERT INTO societe_info (nom, jours_conge_annuel, weekend_days) VALUES ('Ma Société', 18, '0')");
         echo "Default society info inserted.\n";
    }

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage() . "\n");
}
?>
