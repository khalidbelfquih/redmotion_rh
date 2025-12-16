<?php
include_once __DIR__ . '/function.php';

$pdo = getPDO();

try {
    // Check if column 'duree' exists in 'conge_type'
    $stmt = $pdo->query("SHOW COLUMNS FROM conge_type LIKE 'duree'");
    $exists = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$exists) {
        $sql = "ALTER TABLE conge_type ADD COLUMN duree INT DEFAULT 0 COMMENT 'Durée en jours'";
        $pdo->exec($sql);
        echo "Column 'duree' added to table 'conge_type'.\n";
    } else {
        echo "Column 'duree' already exists in table 'conge_type'.\n";
    }

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage() . "\n");
}
?>
