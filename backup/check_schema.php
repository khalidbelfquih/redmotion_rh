<?php
include 'config/connexion.php';

try {
    $sql = "DESCRIBE employes";
    $req = $connexion->query($sql);
    $columns = $req->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in employes table:\n";
    print_r($columns);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
