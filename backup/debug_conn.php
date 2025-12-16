<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting...<br>";
$connexion = include 'model/connexion.php';

if ($connexion) {
    echo "Connection object returned.<br>";
} else {
    echo "Connection object NOT returned. Checking variable...<br>";
    if (isset($connexion)) {
        echo "Variable \$connexion is set.<br>";
    } else {
        echo "Variable \$connexion is NOT set.<br>";
        // Try to manually connect
        try {
            $nom_serveur = "localhost";
            $nom_base_de_donne = "khaldi";
            $utilisateur = "root";
            $motpass = "root";
            $connexion = new PDO("mysql:host=$nom_serveur;dbname=$nom_base_de_donne", $utilisateur, $motpass);
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Manual connection successful.<br>";
        } catch (Exception $e) {
            echo "Manual connection failed: " . $e->getMessage() . "<br>";
        }
    }
}

if (isset($connexion)) {
    echo "<h2>Fixing Client Table</h2>";
    try {
        // Check if PK exists
        $sql = "SHOW KEYS FROM client WHERE Key_name = 'PRIMARY'";
        $q = $connexion->query($sql);
        if ($q->rowCount() == 0) {
            echo "No Primary Key found. Attempting to add one...<br>";
            $sql = "ALTER TABLE client MODIFY id INT AUTO_INCREMENT PRIMARY KEY";
            $connexion->exec($sql);
            echo "Primary Key added successfully.<br>";
        } else {
            echo "Primary Key already exists.<br>";
        }
    } catch (PDOException $e) {
        echo "Error fixing client table: " . $e->getMessage() . "<br>";
    }
}
?>
