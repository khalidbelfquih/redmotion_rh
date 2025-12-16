<?php
// model/planning_functions.php

function getEvents($start, $end) {
    global $connexion;
    $sql = "SELECT * FROM events WHERE date_debut >= ? AND date_fin <= ?";
    $req = $connexion->prepare($sql);
    $req->execute([$start, $end]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function addEvent($data) {
    global $connexion;
    $sql = "INSERT INTO events (titre, description, date_debut, date_fin, type) VALUES (?, ?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['titre'],
        $data['description'],
        $data['date_debut'],
        $data['date_fin'],
        $data['type']
    ]);
}

function updateEvent($id, $data) {
    global $connexion;
    $sql = "UPDATE events SET titre = ?, description = ?, date_debut = ?, date_fin = ?, type = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['titre'],
        $data['description'],
        $data['date_debut'],
        $data['date_fin'],
        $data['type'],
        $id
    ]);
}

function deleteEvent($id) {
    global $connexion;
    $sql = "DELETE FROM events WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id]);
}

function getApprovedLeavesForCalendar($start, $end) {
    global $connexion;
    $sql = "SELECT c.*, e.nom, e.prenom 
            FROM conges c 
            JOIN employes e ON c.id_employe = e.id 
            WHERE c.statut = 'Approuvé' 
            AND (
                (c.date_debut BETWEEN ? AND ?) OR 
                (c.date_fin BETWEEN ? AND ?) OR
                (c.date_debut <= ? AND c.date_fin >= ?)
            )";
    $req = $connexion->prepare($sql);
    $req->execute([$start, $end, $start, $end, $start, $end]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

// --- Presence Functions ---

function addPresence($data) {
    global $connexion;
    $sql = "INSERT INTO planning_presence (id_employe, date_planning, heure_debut, heure_fin) VALUES (?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['id_employe'],
        $data['date_planning'],
        $data['heure_debut'],
        $data['heure_fin']
    ]);
}

function getPresences($start, $end) {
    global $connexion;
    $sql = "SELECT p.*, e.nom, e.prenom 
            FROM planning_presence p
            JOIN employes e ON p.id_employe = e.id
            WHERE p.date_planning BETWEEN ? AND ?";
    $req = $connexion->prepare($sql);
    $req->execute([$start, $end]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function deletePresence($id) {
    global $connexion;
    $sql = "DELETE FROM planning_presence WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id]);
}
?>
