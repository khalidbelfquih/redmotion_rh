<?php
// Fichier model/ajoutPaiement.php
include_once 'connexion.php';
include_once 'function.php';

// Fonction pour ajouter un paiement et ses échéanciers le cas échéant
function ajouterPaiement($id_vente, $montant_total, $montant_paye, $reste_a_payer, $mode_paiement, $reference_paiement = null, $notes = null, $echeances = null) {
    global $connexion;
    
    try {
        // Déterminer le statut du paiement
        $statut = 'en_attente';
        if ($reste_a_payer <= 0) {
            $statut = 'complet';
        } else if ($montant_paye > 0) {
            $statut = 'partiel';
        }
        
        // Insérer le paiement
        $sql = "INSERT INTO paiement (id_vente, montant_total, montant_paye, reste_a_payer, mode_paiement, reference_paiement, date_paiement, statut, notes) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
        
        $req = $connexion->prepare($sql);
        $req->execute([
            $id_vente,
            $montant_total,
            $montant_paye,
            $reste_a_payer,
            $mode_paiement,
            $reference_paiement,
            $statut,
            $notes
        ]);
        
        $id_paiement = $connexion->lastInsertId();
        
        // Si mode de paiement est crédit et des échéances sont fournies, les ajouter
        if ($mode_paiement === 'credit' && !empty($echeances) && is_array($echeances)) {
            foreach ($echeances as $echeance) {
                $date_echeance = $echeance['date'];
                $montant = $echeance['montant'];
                
                $sql = "INSERT INTO echeancier (id_paiement, montant, date_echeance, statut) 
                        VALUES (?, ?, ?, 'a_venir')";
                
                $req = $connexion->prepare($sql);
                $req->execute([
                    $id_paiement,
                    $montant,
                    $date_echeance
                ]);
            }
        }
        
        return $id_paiement;
    } catch (Exception $e) {
        throw new Exception("Erreur lors de l'enregistrement du paiement : " . $e->getMessage());
    }
}

// Fonction pour mettre à jour un paiement existant
function updatePaiement($id_paiement, $montant_paye_additionnel, $reference_paiement = null, $mode_paiement = null) {
    global $connexion;
    
    try {
        // Récupérer les informations du paiement actuel
        $sql = "SELECT montant_total, montant_paye, reste_a_payer FROM paiement WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id_paiement]);
        $paiement = $req->fetch(PDO::FETCH_ASSOC);
        
        if (!$paiement) {
            throw new Exception("Paiement introuvable");
        }
        
        // Calculer les nouveaux montants
        $nouveau_montant_paye = $paiement['montant_paye'] + $montant_paye_additionnel;
        $nouveau_reste_a_payer = $paiement['montant_total'] - $nouveau_montant_paye;
        
        // Déterminer le nouveau statut
        $statut = 'en_attente';
        if ($nouveau_reste_a_payer <= 0) {
            $statut = 'complet';
        } else if ($nouveau_montant_paye > 0) {
            $statut = 'partiel';
        }
        
        // Mettre à jour le paiement
        $sql = "UPDATE paiement SET 
                montant_paye = ?, 
                reste_a_payer = ?, 
                statut = ?";
        
        $params = [
            $nouveau_montant_paye,
            $nouveau_reste_a_payer,
            $statut
        ];
        
        // Ajouter les paramètres optionnels s'ils sont fournis
        if ($reference_paiement !== null) {
            $sql .= ", reference_paiement = ?";
            $params[] = $reference_paiement;
        }
        
        if ($mode_paiement !== null) {
            $sql .= ", mode_paiement = ?";
            $params[] = $mode_paiement;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id_paiement;
        
        $req = $connexion->prepare($sql);
        $req->execute($params);
        
        return true;
    } catch (Exception $e) {
        throw new Exception("Erreur lors de la mise à jour du paiement : " . $e->getMessage());
    }
}