<?php
// model/payroll_functions.php

function getPrimes($id_employe, $mois, $annee) {
    global $connexion;
    $sql = "SELECT * FROM primes WHERE id_employe = ? AND MONTH(date_prime) = ? AND YEAR(date_prime) = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_employe, $mois, $annee]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getDeductions($id_employe, $mois, $annee) {
    global $connexion;
    $sql = "SELECT * FROM deductions WHERE id_employe = ? AND MONTH(date_deduction) = ? AND YEAR(date_deduction) = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_employe, $mois, $annee]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function addPrime($data) {
    global $connexion;
    $sql = "INSERT INTO primes (id_employe, montant, motif, date_prime) VALUES (?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    return $req->execute([$data['id_employe'], $data['montant'], $data['motif'], $data['date_prime']]);
}

function addDeduction($data) {
    global $connexion;
    $sql = "INSERT INTO deductions (id_employe, montant, motif, date_deduction) VALUES (?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    return $req->execute([$data['id_employe'], $data['montant'], $data['motif'], $data['date_deduction']]);
}

function enregistrerPaiement($data) {
    global $connexion;
    // Vérifier si un paiement existe déjà pour ce mois
    $sqlCheck = "SELECT id FROM paiements_salaire WHERE id_employe = ? AND mois = ? AND annee = ?";
    $reqCheck = $connexion->prepare($sqlCheck);
    $reqCheck->execute([$data['id_employe'], $data['mois'], $data['annee']]);
    
    if ($reqCheck->rowCount() > 0) {
        return false; // Déjà payé
    }

    $sql = "INSERT INTO paiements_salaire (id_employe, mois, annee, salaire_base, total_primes, total_deductions, salaire_net, statut) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['id_employe'],
        $data['mois'],
        $data['annee'],
        $data['salaire_base'],
        $data['total_primes'],
        $data['total_deductions'],
        $data['salaire_net'],
        'Payé'
    ]);
}

function getHistoriquePaiements($id_employe = null) {
    global $connexion;
    $sql = "SELECT p.*, e.nom, e.prenom 
            FROM paiements_salaire p 
            JOIN employes e ON p.id_employe = e.id";
    
    if ($id_employe) {
        $sql .= " WHERE p.id_employe = ?";
    }
    
    $sql .= " ORDER BY p.date_paiement DESC";
    
    $req = $connexion->prepare($sql);
    if ($id_employe) {
        $req->execute([$id_employe]);
    } else {
        $req->execute();
    }
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getStatistiquesPaie($mois, $annee) {
    global $connexion;
    $sql = "SELECT 
                SUM(salaire_net) as total_verse,
                COUNT(id) as nombre_paiements
            FROM paiements_salaire 
            WHERE mois = ? AND annee = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$mois, $annee]);
    return $req->fetch(PDO::FETCH_ASSOC);
}
function checkPaiementStatus($id_employe, $mois, $annee) {
    global $connexion;
    $sql = "SELECT id FROM paiements_salaire WHERE id_employe = ? AND mois = ? AND annee = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_employe, $mois, $annee]);
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : false;
}

function getEmployesAvecStatutPaie($mois, $annee, $recherche = '') {
    global $connexion;
    // Récupérer tous les employés actifs avec leur poste
    $sql = "SELECT e.*, p.titre as poste, d.nom as departement 
            FROM employes e 
            LEFT JOIN postes p ON e.id_poste = p.id 
            LEFT JOIN departements d ON p.id_departement = d.id 
            WHERE e.statut = 'Actif'";
            
    $params = [];
    
    if (!empty($recherche)) {
        $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.email LIKE ?)";
        $term = "%$recherche%";
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }
    
    $sql .= " ORDER BY e.nom ASC";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    $employes = $req->fetchAll(PDO::FETCH_ASSOC);
    
    // Ajouter le statut de paiement pour chaque employé
    foreach ($employes as &$emp) {
        $paiementId = checkPaiementStatus($emp['id'], $mois, $annee);
        $emp['est_paye'] = (bool)$paiementId;
        $emp['id_paiement'] = $paiementId;
    }
    
    return $employes;
}

function getDetailedPaymentHistory($id_employe) {
    global $connexion;
    
    // Fetch basic payment info
    $sql = "SELECT p.*, e.nom, e.prenom 
            FROM paiements_salaire p 
            JOIN employes e ON p.id_employe = e.id 
            WHERE p.id_employe = ? 
            ORDER BY p.annee DESC, p.mois DESC";
            
    $req = $connexion->prepare($sql);
    $req->execute([$id_employe]);
    $payments = $req->fetchAll(PDO::FETCH_ASSOC);
    
    // Enrich with primes and deductions
    foreach ($payments as &$payment) {
        $payment['primes'] = getPrimes($id_employe, $payment['mois'], $payment['annee']);
        $payment['deductions'] = getDeductions($id_employe, $payment['mois'], $payment['annee']);
    }
    
    return $payments;
}

function getPaiementById($id) {
    global $connexion;
    
    // Fetch payment info with employee details
    $sql = "SELECT p.*, e.nom, e.prenom, e.email, e.cnss, e.date_embauche, e.salaire as salaire_contractuel,
                   po.titre as poste, d.nom as departement
            FROM paiements_salaire p 
            JOIN employes e ON p.id_employe = e.id 
            LEFT JOIN postes po ON e.id_poste = po.id
            LEFT JOIN departements d ON po.id_departement = d.id
            WHERE p.id = ?";
            
    $req = $connexion->prepare($sql);
    $req->execute([$id]);
    $paiement = $req->fetch(PDO::FETCH_ASSOC);
    
    if ($paiement) {
        $paiement['primes'] = getPrimes($paiement['id_employe'], $paiement['mois'], $paiement['annee']);
        $paiement['deductions'] = getDeductions($paiement['id_employe'], $paiement['mois'], $paiement['annee']);
    }
    
    return $paiement;
}
?>
