<?php
// model/role_functions.php

function getRoles($id = null) {
    global $connexion;
    if ($id) {
        $stmt = $connexion->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt = $connexion->query("SELECT * FROM roles ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

function ajouterRole($nom, $description) {
    global $connexion;
    $sql = "INSERT INTO roles (nom, description) VALUES (?, ?)";
    $stmt = $connexion->prepare($sql);
    return $stmt->execute([$nom, $description]);
}

function modifierRole($id, $nom, $description) {
    global $connexion;
    $sql = "UPDATE roles SET nom = ?, description = ? WHERE id = ?";
    $stmt = $connexion->prepare($sql);
    return $stmt->execute([$nom, $description, $id]);
}

function supprimerRole($id) {
    global $connexion;
    
    // Vérifier si le rôle est assigné à des utilisateurs
    // Note: Cela suppose que la table 'utilisateurs' a une colonne 'role' qui stocke le NOM du rôle (comme actuellement) ou l'ID.
    // VERIFICATION: Actuellement 'utilisateurs' stocke le nom ('admin', 'utilisateur').
    // On doit d'abord récupérer le nom du rôle pour vérifier.
    
    $role = getRoles($id);
    if (!$role) return false;
    
    $nomRole = $role['nom'];
    
    $stmtcheck = $connexion->prepare("SELECT COUNT(*) FROM utilisateur WHERE role = ?");
    $stmtcheck->execute([$nomRole]);
    if ($stmtcheck->fetchColumn() > 0) {
        return "assigned"; // Le rôle est utilisé
    }

    $sql = "DELETE FROM roles WHERE id = ?";
    $stmt = $connexion->prepare($sql);
    return $stmt->execute([$id]);
}
?>
