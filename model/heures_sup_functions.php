<?php
// model/heures_sup_functions.php

function getHeuresSup($filtres = []) {
    global $connexion;
    $sql = "SELECT h.*, e.nom, e.prenom, e.photo 
            FROM heures_sup h 
            JOIN employes e ON h.id_employe = e.id 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filtres['id_employe'])) {
        $sql .= " AND h.id_employe = ?";
        $params[] = $filtres['id_employe'];
    }
    
    if (!empty($filtres['mois']) && !empty($filtres['annee'])) {
        $sql .= " AND MONTH(h.date_heure) = ? AND YEAR(h.date_heure) = ?";
        $params[] = $filtres['mois'];
        $params[] = $filtres['annee'];
    }
    
    $sql .= " ORDER BY h.date_heure DESC";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function addHeureSup($data) {
    global $connexion;
    $sql = "INSERT INTO heures_sup (id_employe, date_heure, duree_minutes, motif, valide) VALUES (?, ?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['id_employe'],
        $data['date_heure'],
        $data['duree_minutes'],
        $data['motif'],
        $data['valide'] ?? 0
    ]);
}

function updateHeureSup($id, $data) {
    global $connexion;
    $sql = "UPDATE heures_sup SET date_heure = ?, duree_minutes = ?, motif = ?, valide = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['date_heure'],
        $data['duree_minutes'],
        $data['motif'],
        $data['valide'],
        $id
    ]);
}

function deleteHeureSup($id) {
    global $connexion;
    $sql = "DELETE FROM heures_sup WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id]);
}

function getHeureSupStats($mois, $annee) {
    global $connexion;
    
    // Total heures sup
    $sqlTotal = "SELECT COUNT(*) as total, SUM(duree_minutes) as total_minutes FROM heures_sup WHERE MONTH(date_heure) = ? AND YEAR(date_heure) = ?";
    $reqTotal = $connexion->prepare($sqlTotal);
    $reqTotal->execute([$mois, $annee]);
    $total = $reqTotal->fetch(PDO::FETCH_ASSOC);
    
    return [
        'total_count' => $total['total'],
        'total_minutes' => $total['total_minutes']
    ];
}

function getTotalHeuresSupMinutes($id_employe, $mois, $annee) {
    global $connexion;
    $sql = "SELECT SUM(duree_minutes) as total_minutes, COUNT(*) as nb_heures_sup 
            FROM heures_sup 
            WHERE id_employe = ? 
            AND valide = 1
            AND MONTH(date_heure) = ? AND YEAR(date_heure) = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_employe, $mois, $annee]);
    return $req->fetch(PDO::FETCH_ASSOC);
}

?>
