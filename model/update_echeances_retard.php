<?php
// Créer le fichier model/update_echeances_retard.php
include 'connexion.php';

try {
    // Date du jour
    $date_actuelle = date('Y-m-d');
    
    // Mettre à jour les échéances qui sont en retard
    $sql = "UPDATE echeancier 
            SET statut = 'en_retard' 
            WHERE statut = 'a_venir' 
            AND date_echeance < ?";
            
    $req = $connexion->prepare($sql);
    $req->execute([$date_actuelle]);
    
    $nb_echeances_retard = $req->rowCount();
    
    echo "Mise à jour terminée. $nb_echeances_retard échéances marquées comme en retard.";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>