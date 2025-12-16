<?php
include '../model/function.php';

if (isset($_GET['id'])) {
    $client = getClient($_GET['id']);
    header('Content-Type: application/json');
    echo json_encode($client);
}
?>
