<?php
require_once 'config/connexion.php';

try {
    echo "Employee with ID 0:\n";
    $stmt = $connexion->query("SELECT * FROM employes WHERE id=0");
    $emp = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($emp);
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
