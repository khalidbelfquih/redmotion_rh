<?php
// Fichier model/traitementPaiement.php
include_once 'connexion.php';
include_once 'function.php';
include_once 'ajoutPaiement.php';

// Traitement du formulaire de vente pour ajouter le paiement
if (isset($_POST['id_vente']) && !empty($_POST['id_vente'])) {
    try {
        $id_vente = $_POST['id_vente'];
        $montant_total = $_POST['montant_total'];
        $montant_paye = $_POST['montant_paye'];
        $reste_a_payer = $_POST['reste_a_payer'];
        $mode_paiement = $_POST['mode_paiement'];
        $reference_paiement = $_POST['reference_paiement'] ?? null;
        $notes_paiement = $_POST['notes_paiement'] ?? null;
        
        // Traiter les échéances si mode de paiement est crédit
        $echeances = [];
        if ($mode_paiement === 'credit' && isset($_POST['echeance_date']) && isset($_POST['echeance_montant'])) {
            $dates = $_POST['echeance_date'];
            $montants = $_POST['echeance_montant'];
            
            for ($i = 0; $i < count($dates); $i++) {
                if (!empty($dates[$i]) && !empty($montants[$i])) {
                    $echeances[] = [
                        'date' => $dates[$i],
                        'montant' => $montants[$i]
                    ];
                }
            }
        }
        
        // Ajouter le paiement
        $id_paiement = ajouterPaiement(
            $id_vente, 
            $montant_total, 
            $montant_paye, 
            $reste_a_payer, 
            $mode_paiement, 
            $reference_paiement, 
            $notes_paiement, 
            $echeances
        );
        
        $_SESSION['message']['text'] = "Paiement enregistré avec succès";
        $_SESSION['message']['type'] = "success";
        
        // Rediriger vers la page de détail de vente
        header("Location: ../vue/detail_vente.php?id=" . $id_vente);
        exit();
    } catch (Exception $e) {
        $_SESSION['message']['text'] = "Erreur: " . $e->getMessage();
        $_SESSION['message']['type'] = "danger";
        
        // Rediriger vers la page de vente
        header("Location: ../vue/vente.php");
        exit();
    }
} else {
    // Redirection si accès direct au script
    $_SESSION['message']['text'] = "Accès non autorisé";
    $_SESSION['message']['type'] = "danger";
    header("Location: ../vue/vente.php");
    exit();
}