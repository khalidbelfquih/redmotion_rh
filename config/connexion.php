<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'error_handler.php';

$nom_serveur = getenv('DB_HOST') ?: "localhost";
$nom_base_de_donne = getenv('DB_NAME') ?: "redmotion_rh";
$utilisateur = getenv('DB_USER') ?: "root";
$motpass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";

try {
    $connexion = new PDO("mysql:host=$nom_serveur;dbname=$nom_base_de_donne", $utilisateur, $motpass);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $connexion;
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
