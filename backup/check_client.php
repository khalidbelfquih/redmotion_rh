<?php
include 'model/connexion.php';
$sql = "DESCRIBE client";
$q = $connexion->query($sql);
while($row = $q->fetch(PDO::FETCH_ASSOC)){
    print_r($row);
}

$sql = "SHOW CREATE TABLE client";
$q = $connexion->query($sql);
$row = $q->fetch(PDO::FETCH_ASSOC);
print_r($row);
?>
