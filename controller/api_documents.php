<?php
include '../model/hr_functions.php';

header('Content-Type: application/json');

if (!isset($_GET['id_employe'])) {
    echo json_encode(['error' => 'ID employé manquant']);
    exit;
}

$id_employe = $_GET['id_employe'];
$documents = getDocumentsByEmploye($id_employe);

echo json_encode($documents);
?>
