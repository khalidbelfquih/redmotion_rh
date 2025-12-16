<?php
session_start();
// Utiliser le chemin absolu pour inclure le fichier de fonctions
include_once(__DIR__ . '/function.php');

// Vérifier si les champs obligatoires sont remplis et l'ID est présent
if (
    !empty($_POST['id']) && 
    !empty($_POST['nom']) && 
    !empty($_POST['prenom']) && 
    !empty($_POST['telephone']) && 
    !empty($_POST['adresse'])
) {
    // Préparer les données
    $data = [
        'id' => $_POST['id'],
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'telephone' => $_POST['telephone'],
        'adresse' => $_POST['adresse'],
        'societe' => !empty($_POST['societe']) ? $_POST['societe'] : null,
        'ice' => !empty($_POST['ice']) ? $_POST['ice'] : null,
        'email' => !empty($_POST['email']) ? $_POST['email'] : null,
        'site_web' => !empty($_POST['site_web']) ? $_POST['site_web'] : null
    ];
    
    // Modifier le fournisseur
    if(modifFournisseur($data)) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Fournisseur modifié avec succès!',
            'context' => 'fournisseur'
        ];
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Erreur lors de la modification du fournisseur.',
            'context' => 'fournisseur'
        ];
    }
} else {
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Veuillez remplir tous les champs obligatoires.',
        'context' => 'fournisseur'
    ];
}

// Rediriger vers la page de fournisseur
header('Location: ../vue/commande.php');
exit;