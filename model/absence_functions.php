<?php
// model/absence_functions.php

function getAbsences($filtres = []) {
    global $connexion;
    $sql = "SELECT a.*, e.nom, e.prenom, e.photo 
            FROM suivi_absence a 
            JOIN employes e ON a.id_employe = e.id 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filtres['id_employe'])) {
        $sql .= " AND a.id_employe = ?";
        $params[] = $filtres['id_employe'];
    }
    
    if (!empty($filtres['mois']) && !empty($filtres['annee'])) {
        // Filter by start date falling in the month/year
        $sql .= " AND MONTH(a.date_debut) = ? AND YEAR(a.date_debut) = ?";
        $params[] = $filtres['mois'];
        $params[] = $filtres['annee'];
    }
    
    $sql .= " ORDER BY a.date_debut DESC";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function addAbsence($data) {
    global $connexion;
    $sql = "INSERT INTO suivi_absence (id_employe, date_debut, date_fin, type_absence, motif, justifie) VALUES (?, ?, ?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['id_employe'],
        $data['date_debut'],
        $data['date_fin'],
        $data['type_absence'],
        $data['motif'],
        $data['justifie']
    ]);
}

function updateAbsence($id, $data) {
    global $connexion;
    $sql = "UPDATE suivi_absence SET date_debut = ?, date_fin = ?, type_absence = ?, motif = ?, justifie = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['date_debut'],
        $data['date_fin'],
        $data['type_absence'],
        $data['motif'],
        $data['justifie'],
        $id
    ]);
}

function deleteAbsence($id) {
    global $connexion;
    $sql = "DELETE FROM suivi_absence WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id]);
}

function getAbsenceStats($mois, $annee) {
    global $connexion;
    
    // Total absences count
    $sqlTotal = "SELECT COUNT(*) as total FROM suivi_absence WHERE MONTH(date_debut) = ? AND YEAR(date_debut) = ?";
    $reqTotal = $connexion->prepare($sqlTotal);
    $reqTotal->execute([$mois, $annee]);
    $total = $reqTotal->fetch(PDO::FETCH_ASSOC);
    
    // Total days lost (approximate, simple diff)
    $sqlDays = "SELECT SUM(DATEDIFF(date_fin, date_debut) + 1) as total_days 
                FROM suivi_absence 
                WHERE MONTH(date_debut) = ? AND YEAR(date_debut) = ?";
    $reqDays = $connexion->prepare($sqlDays);
    $reqDays->execute([$mois, $annee]);
    $days = $reqDays->fetch(PDO::FETCH_ASSOC);

    // Unjustified absences count
    $sqlUnjustified = "SELECT COUNT(*) as total FROM suivi_absence WHERE justifie = 0 AND MONTH(date_debut) = ? AND YEAR(date_debut) = ?";
    $reqUnjustified = $connexion->prepare($sqlUnjustified);
    $reqUnjustified->execute([$mois, $annee]);
    $unjustified = $reqUnjustified->fetch(PDO::FETCH_ASSOC);
    
    return [
        'total_absences' => $total['total'],
        'total_days' => $days['total_days'] ?? 0,
        'unjustified_count' => $unjustified['total']
    ];
}

function getAbsenceById($id) {
    global $connexion;
    $sql = "SELECT * FROM suivi_absence WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id]);
    return $req->fetch(PDO::FETCH_ASSOC);
}
?>
