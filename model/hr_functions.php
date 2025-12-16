<?php
include_once __DIR__ . '/../config/connexion.php';

// --- Employés ---

function getEmployes($filtres = []) {
    global $connexion;
    $sql = "SELECT e.*, p.titre as poste, p.id_departement, d.nom as departement 
            FROM employes e 
            LEFT JOIN postes p ON e.id_poste = p.id 
            LEFT JOIN departements d ON p.id_departement = d.id 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filtres['recherche'])) {
        $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR e.email LIKE ?)";
        $recherche = "%" . $filtres['recherche'] . "%";
        $params[] = $recherche;
        $params[] = $recherche;
        $params[] = $recherche;
    }
    
    if (!empty($filtres['departement'])) {
        $sql .= " AND d.id = ?";
        $params[] = $filtres['departement'];
    }
    
    if (!empty($filtres['statut'])) {
        $sql .= " AND e.statut = ?";
        $params[] = $filtres['statut'];
    }
    
    $sql .= " ORDER BY e.nom ASC";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getEmploye($id) {
    global $connexion;
    $sql = "SELECT e.*, p.titre as poste, p.id as id_poste, d.nom as departement, d.id as id_departement 
            FROM employes e 
            LEFT JOIN postes p ON e.id_poste = p.id 
            LEFT JOIN departements d ON p.id_departement = d.id 
            WHERE e.id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id]);
    return $req->fetch(PDO::FETCH_ASSOC);
}

function addEmploye($data) {
    global $connexion;
    $sql = "INSERT INTO employes (nom, prenom, email, telephone, date_naissance, date_embauche, salaire, id_poste, statut, adresse, cin, cnss, photo, situation_familiale, nombre_enfants, type_contrat, rib) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    $req->execute([
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $data['telephone'],
        $data['date_naissance'],
        $data['date_embauche'],
        $data['salaire'],
        $data['id_poste'],
        $data['statut'],
        $data['adresse'],
        $data['cin'],
        $data['cnss'],
        $data['photo'],
        $data['situation_familiale'],
        $data['nombre_enfants'],
        $data['type_contrat'],
        $data['rib']
    ]);
    return $connexion->lastInsertId();
}

function updateEmploye($id, $data) {
    global $connexion;
    $sql = "UPDATE employes SET 
            nom = ?, prenom = ?, email = ?, telephone = ?, date_naissance = ?, date_embauche = ?, 
            salaire = ?, id_poste = ?, statut = ?, adresse = ?, cin = ?, cnss = ?, 
            situation_familiale = ?, nombre_enfants = ?, type_contrat = ?, rib = ?";
    
    $params = [
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $data['telephone'],
        $data['date_naissance'],
        $data['date_embauche'],
        $data['salaire'],
        $data['id_poste'],
        $data['statut'],
        $data['adresse'],
        $data['cin'],
        $data['cnss'],
        $data['situation_familiale'],
        $data['nombre_enfants'],
        $data['type_contrat'],
        $data['rib']
    ];
    
    if (!empty($data['photo'])) {
        $sql .= ", photo = ?";
        $params[] = $data['photo'];
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $id;
    
    $req = $connexion->prepare($sql);
    return $req->execute($params);
}

function deleteEmploye($id) {
    global $connexion;
    $sql = "DELETE FROM employes WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id]);
}

// --- Départements & Postes ---

function getDepartements() {
    global $connexion;
    $sql = "SELECT * FROM departements ORDER BY nom ASC";
    $req = $connexion->query($sql);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getPostes($id_departement = null) {
    global $connexion;
    $sql = "SELECT * FROM postes";
    $params = [];
    
    if ($id_departement) {
        $sql .= " WHERE id_departement = ?";
        $params[] = $id_departement;
    }
    
    $sql .= " ORDER BY titre ASC";
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

// --- Candidats ---

function getCandidats($filtres = []) {
    global $connexion;
    $sql = "SELECT * FROM candidats WHERE 1=1";
    $params = [];
    
    if (!empty($filtres['statut'])) {
        $sql .= " AND statut = ?";
        $params[] = $filtres['statut'];
    }
    
    $sql .= " ORDER BY date_candidature DESC";
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function addCandidat($data) {
    global $connexion;
    $sql = "INSERT INTO candidats (nom, prenom, email, telephone, poste_vise, cv_path, statut, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    $req->execute([
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $data['telephone'],
        $data['poste_vise'],
        $data['cv_path'],
        $data['statut'],
        $data['notes']
    ]);
    return $connexion->lastInsertId();
}

function updateCandidat($data) {
    global $connexion;
    $sql = "UPDATE candidats SET nom = ?, prenom = ?, email = ?, telephone = ?, poste_vise = ?, statut = ?, notes = ? WHERE id = ?";
    $params = [
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $data['telephone'],
        $data['poste_vise'],
        $data['statut'],
        $data['notes'],
        $data['id']
    ];
    
    // Only update CV if a new one is provided
    if (!empty($data['cv_path'])) {
        $sql = "UPDATE candidats SET nom = ?, prenom = ?, email = ?, telephone = ?, poste_vise = ?, statut = ?, notes = ?, cv_path = ? WHERE id = ?";
        $params = [
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['poste_vise'],
            $data['statut'],
            $data['notes'],
            $data['cv_path'],
            $data['id']
        ];
    }
    
    $req = $connexion->prepare($sql);
    return $req->execute($params);
}

function updateStatutCandidat($id, $statut) {
    global $connexion;
    $sql = "UPDATE candidats SET statut = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$statut, $id]);
}

function updateStatutEmploye($id, $statut) {
    global $connexion;
    $sql = "UPDATE employes SET statut = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$statut, $id]);
}

// --- Documents Employés ---

// --- Documents Employés ---

function addDocument($data) {
    global $connexion;
    // Modified to insert into file_data instead of fichier path
    // We insert NULL for 'fichier' column to maintain compatibility if needed, or simply ignore it
    $sql = "INSERT INTO employe_documents (id_employe, titre, type, file_data, mime_type, file_name, fichier) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    return $req->execute([
        $data['id_employe'], 
        $data['titre'], 
        $data['type'], 
        $data['file_data'],
        $data['mime_type'],
        $data['file_name'],
        null // We are not using the file path anymore
    ]);
}

function getDocumentsByEmploye($id_employe) {
    global $connexion;
    // We select everything EXCEPT the heavy file_data for the list view to improve performance
    $sql = "SELECT id, id_employe, titre, type, file_name, mime_type, date_ajout FROM employe_documents WHERE id_employe = ? ORDER BY date_ajout DESC";
    $req = $connexion->prepare($sql);
    $req->execute([$id_employe]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getDocumentContent($id) {
    global $connexion;
    $sql = "SELECT file_data, mime_type, file_name FROM employe_documents WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id]);
    return $req->fetch(PDO::FETCH_ASSOC);
}

function deleteDocument($id) {
    global $connexion;
    // No need to unlink physical file anymore since we store in DB
    $sql = "DELETE FROM employe_documents WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id]);
}
?>
