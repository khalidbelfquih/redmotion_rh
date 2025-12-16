<?php
include 'model/connexion.php';
$tables = ['client', 'voiture', 'location', 'paiement_location'];
foreach ($tables as $table) {
    try {
        $sql = "SELECT 1 FROM $table LIMIT 1";
        $connexion->query($sql);
        echo "$table: OK<br>";
    } catch (Exception $e) {
        echo "$table: MISSING or Error (" . $e->getMessage() . ")<br>";
    }
}
?>
