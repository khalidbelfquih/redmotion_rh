<?php
include 'config/connexion.php';
$req = $connexion->query("SHOW TABLES");
$tables = $req->fetchAll(PDO::FETCH_COLUMN);
echo "TABLES_START\n";
foreach ($tables as $table) {
    echo $table . "\n";
}
echo "TABLES_END\n";
?>
