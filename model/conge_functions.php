<?php
include_once __DIR__ . '/../config/connexion.php';

function addConge($data) {
    global $connexion;
    $sql = "INSERT INTO conges (id_employe, type_conge, date_debut, date_fin, motif, justificatif) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    $req->execute([
        $data['id_employe'],
        $data['type_conge'],
        $data['date_debut'],
        $data['date_fin'],
        $data['motif'],
        $data['justificatif']
    ]);
    return $connexion->lastInsertId();
}

function getConges($filtres = []) {
    global $connexion;
    $sql = "SELECT c.*, e.nom, e.prenom, e.photo, e.statut as employe_statut 
            FROM conges c 
            JOIN employes e ON c.id_employe = e.id 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filtres['id_employe'])) {
        $sql .= " AND c.id_employe = ?";
        $params[] = $filtres['id_employe'];
    }
    
    if (!empty($filtres['statut'])) {
        $sql .= " AND c.statut = ?";
        $params[] = $filtres['statut'];
    }
    
    $sql .= " ORDER BY c.date_demande DESC";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function updateStatutConge($id, $statut, $commentaire = null) {
    global $connexion;
    $sql = "UPDATE conges SET statut = ?, commentaire_admin = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$statut, $commentaire, $id]);
}

function getCongeById($id) {
    global $connexion;
    $sql = "SELECT c.*, e.nom, e.prenom, e.email 
            FROM conges c 
            JOIN employes e ON c.id_employe = e.id 
            WHERE c.id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id]);
    return $req->fetch(PDO::FETCH_ASSOC);
}

function deleteConge($id) {
    global $connexion;
    $sql = "DELETE FROM conges WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id]);
}

function updateConge($data) {
    global $connexion;
    $sql = "UPDATE conges SET 
            id_employe = ?, 
            type_conge = ?, 
            date_debut = ?, 
            date_fin = ?, 
            motif = ? 
            WHERE id = ?";
    $params = [
        $data['id_employe'],
        $data['type_conge'],
        $data['date_debut'],
        $data['date_fin'],
        $data['motif'],
        $data['id']
    ];
    
    if (isset($data['justificatif'])) {
        $sql = "UPDATE conges SET 
                id_employe = ?, 
                type_conge = ?, 
                date_debut = ?, 
                date_fin = ?, 
                motif = ?,
                justificatif = ?
                WHERE id = ?";
        $params = [
            $data['id_employe'],
            $data['type_conge'],
            $data['date_debut'],
            $data['date_fin'],
            $data['motif'],
            $data['justificatif'],
            $data['id']
        ];
    }
    
    $req = $connexion->prepare($sql);
    return $req->execute($params);
}

function checkCongeOverlap($id_employe, $date_debut, $date_fin, $exclude_id = null) {
    global $connexion;
    $sql = "SELECT COUNT(*) FROM conges 
            WHERE id_employe = ? 
            AND statut != 'Refusé'
            AND (
                (date_debut <= ? AND date_fin >= ?) OR 
                (date_debut <= ? AND date_fin >= ?) OR
                (date_debut >= ? AND date_fin <= ?)
            )";
    $params = [$id_employe, $date_fin, $date_debut, $date_fin, $date_debut, $date_debut, $date_fin];
    
    if ($exclude_id) {
        $sql .= " AND id != ?";
        $params[] = $exclude_id;
    }
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchColumn() > 0;
}

function getCongesByEmployeYear($id_employe, $year) {
    global $connexion;
    $sql = "SELECT * FROM conges 
            WHERE id_employe = ? 
            AND YEAR(date_debut) = ? 
            ORDER BY date_debut DESC";
    $req = $connexion->prepare($sql);
    $req->execute([$id_employe, $year]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getExpiringConges() {
    global $connexion;
    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    
    $sql = "SELECT c.*, e.nom, e.prenom 
            FROM conges c 
            JOIN employes e ON c.id_employe = e.id 
            WHERE c.statut = 'Approuvé' 
            AND (c.date_fin = ? OR c.date_fin = ?)";
            
    $req = $connexion->prepare($sql);
    $req->execute([$today, $tomorrow]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function markCongeFinished($id) {
    global $connexion;
    $sql = "UPDATE conges SET statut = 'Terminé' WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id]);
}

function getCongeTypes() {
    global $connexion;
    $sql = "SELECT * FROM conge_type ORDER BY nom ASC";
    $req = $connexion->query($sql);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
?>
