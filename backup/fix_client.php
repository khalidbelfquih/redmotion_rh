<?php
include 'model/connexion.php';

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

echo "<h2>Checking Tables</h2>";
$tables = ['client', 'voiture', 'location'];
foreach ($tables as $table) {
    echo "<h3>$table</h3>";
    try {
        $sql = "DESCRIBE $table";
        $q = $connexion->query($sql);
        while($row = $q->fetch(PDO::FETCH_ASSOC)){
            print_r($row);
            echo "<br>";
        }
    } catch (Exception $e) {
        echo "Table $table does not exist or error: " . $e->getMessage() . "<br>";
    }
}
?>
