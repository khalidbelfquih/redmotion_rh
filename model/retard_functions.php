<?php
// model/retard_functions.php

function getRetards($filtres = []) {
    global $connexion;
    $sql = "SELECT r.*, e.nom, e.prenom, e.photo 
            FROM retards r 
            JOIN employes e ON r.id_employe = e.id 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filtres['id_employe'])) {
        $sql .= " AND r.id_employe = ?";
        $params[] = $filtres['id_employe'];
    }
    
    if (!empty($filtres['mois']) && !empty($filtres['annee'])) {
        $sql .= " AND MONTH(r.date_retard) = ? AND YEAR(r.date_retard) = ?";
        $params[] = $filtres['mois'];
        $params[] = $filtres['annee'];
    }
    
    $sql .= " ORDER BY r.date_retard DESC";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function addRetard($data) {
    global $connexion;
    $sql = "INSERT INTO retards (id_employe, date_retard, duree_minutes, motif, justifie) VALUES (?, ?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['id_employe'],
        $data['date_retard'],
        $data['duree_minutes'],
        $data['motif'],
        $data['justifie']
    ]);
}

function updateRetard($id, $data) {
    global $connexion;
    $sql = "UPDATE retards SET date_retard = ?, duree_minutes = ?, motif = ?, justifie = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['date_retard'],
        $data['duree_minutes'],
        $data['motif'],
        $data['justifie'],
        $id
    ]);
}

function deleteRetard($id) {
    global $connexion;
    $sql = "DELETE FROM retards WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id]);
}

function getRetardStats($mois, $annee) {
    global $connexion;
    
    // Total retards
    $sqlTotal = "SELECT COUNT(*) as total, SUM(duree_minutes) as total_minutes FROM retards WHERE MONTH(date_retard) = ? AND YEAR(date_retard) = ?";
    $reqTotal = $connexion->prepare($sqlTotal);
    $reqTotal->execute([$mois, $annee]);
    $total = $reqTotal->fetch(PDO::FETCH_ASSOC);
    
    // Employees at risk (3+ unjustified latenesses in current month)
    $sqlRisk = "SELECT r.id_employe, e.nom, e.prenom, COUNT(*) as nb_retards 
                FROM retards r 
                JOIN employes e ON r.id_employe = e.id
                WHERE r.justifie = 0 AND MONTH(r.date_retard) = ? AND YEAR(r.date_retard) = ?
                GROUP BY r.id_employe 
                HAVING nb_retards >= 3";
    $reqRisk = $connexion->prepare($sqlRisk);
    $reqRisk->execute([$mois, $annee]);
    $risk = $reqRisk->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'total_retards' => $total['total'],
        'total_minutes' => $total['total_minutes'],
        'employes_risk' => $risk
    ];
}

function getRetardById($id) {
    global $connexion;
    $sql = "SELECT r.*, e.nom, e.prenom, e.poste, e.departement 
            FROM retards r 
            JOIN employes e ON r.id_employe = e.id 
            WHERE r.id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id]);
    return $req->fetch(PDO::FETCH_ASSOC);
}

function getUnjustifiedRetardsCount($id_employe, $mois, $annee) {
    global $connexion;
    $sql = "SELECT COUNT(*) as count FROM retards 
            WHERE id_employe = ? AND justifie = 0 
            AND MONTH(date_retard) = ? AND YEAR(date_retard) = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_employe, $mois, $annee]);
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

function getTotalRetardMinutes($id_employe, $mois, $annee) {
    global $connexion;
    $sql = "SELECT SUM(duree_minutes) as total_minutes, COUNT(*) as nb_retards 
            FROM retards 
            WHERE id_employe = ? 
            AND MONTH(date_retard) = ? AND YEAR(date_retard) = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_employe, $mois, $annee]);
    return $req->fetch(PDO::FETCH_ASSOC);
}
?>
