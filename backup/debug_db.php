<?php
include 'config/connexion.php';

// Check DB name
echo "DB: " . $nom_base_de_donne . "<br>";

// Check Table
try {
    $stmt = $connexion->query("DESCRIBE employe_documents");
    echo "Table employe_documents exists.<br>";
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($columns);
} catch (Exception $e) {
    echo "Error describing table: " . $e->getMessage();
}

// Check Rows count
$stmt = $connexion->query("SELECT COUNT(*) as count FROM employe_documents");
$row = $stmt->fetch();
echo "<br>Row count: " . $row['count'];
?>
