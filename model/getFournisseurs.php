<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Démarrer la session si nécessaire
session_start();

// Inclure le fichier de fonctions
include_once(__DIR__ . '/function.php');

try {
    // Récupérer tous les fournisseurs
    $fournisseurs = getFournisseur(); // Utilise votre fonction existante
    
    if ($fournisseurs !== false && is_array($fournisseurs)) {
        echo json_encode([
            'success' => true,
            'fournisseurs' => $fournisseurs,
            'count' => count($fournisseurs)
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'fournisseurs' => [],
            'count' => 0,
            'message' => 'Aucun fournisseur trouvé'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des fournisseurs: ' . $e->getMessage(),
        'fournisseurs' => []
    ]);
}
?>