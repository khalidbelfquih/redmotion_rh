<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$connexion = include 'model/connexion.php';

if (!$connexion) {
    // Fallback connection
    try {
        $nom_serveur = "localhost";
        $nom_base_de_donne = "khaldi";
        $utilisateur = "root";
        $motpass = "root";
        $connexion = new PDO("mysql:host=$nom_serveur;dbname=$nom_base_de_donne", $utilisateur, $motpass);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

echo "<h1>Migration Log</h1>";

// Fix Client Table
try {
    echo "Checking client table...<br>";
    $sql = "ALTER TABLE client MODIFY id INT AUTO_INCREMENT PRIMARY KEY";
    $connexion->exec($sql);
    echo "Client table fixed (PK added).<br>";
} catch (Exception $e) {
    echo "Client table fix note: " . $e->getMessage() . " (This is fine if PK already exists)<br>";
}

// Run Migration
try {
    echo "Running migration.sql...<br>";
    $sql = file_get_contents('migration.sql');
    // Split by semicolon to run statements individually for better error reporting
    $statements = explode(';', $sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $connexion->exec($statement);
            } catch (Exception $e) {
                echo "Error executing statement: " . substr($statement, 0, 50) . "... <br>";
                echo "Error: " . $e->getMessage() . "<br>";
            }
        }
    }
    echo "Migration completed.<br>";
} catch (Exception $e) {
    echo "Critical error during migration: " . $e->getMessage();
}
?>
