<?php
session_start();
// Utiliser le chemin absolu pour inclure le fichier de fonctions
include('function.php');

// Vérifier si l'ID de commande est présent
if (!empty($_GET['idCommande'])) {
    $idCommande = $_GET['idCommande'];
    
    // Marquer la commande comme livrée
    if(livreCommande($idCommande)) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Commande marquée comme livrée avec succès!',
            'context' => 'commande'
        ];
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Erreur lors du changement de statut de la commande.',
            'context' => 'commande'
        ];
    }
} else {
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'ID de commande manquant.',
        'context' => 'commande'
    ];
}

// Rediriger vers la page de commande
header('Location: ../vue/commande.php');
exit;