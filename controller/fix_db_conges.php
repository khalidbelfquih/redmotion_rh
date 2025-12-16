<?php
include '../config/connexion.php';

try {
    // Check if column exists
    $stmt = $connexion->prepare("SHOW COLUMNS FROM conges LIKE 'justificatif'");
    $stmt->execute();
    $col = $stmt->fetch();

    if (!$col) {
        echo "Column 'justificatif' missing. Adding it...<br>";
        $connexion->exec("ALTER TABLE conges ADD COLUMN justificatif VARCHAR(255) DEFAULT NULL AFTER motif");
        echo "Column added successfully.";
    } else {
        echo "Column 'justificatif' already exists.";
    }

    // Also check for user permission / silent errors
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
