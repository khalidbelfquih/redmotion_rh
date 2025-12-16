<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    echo '{"received":"' . strlen($input) . ' characters"}';
} else {
    echo '{"error":"GET not allowed"}';
}
?>