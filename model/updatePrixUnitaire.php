<?php
// model/updatePrixUnitaire.php
include 'connexion.php';

// Vérifier que la demande est une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données envoyées
    $id_article = isset($_POST['id_article']) ? intval($_POST['id_article']) : 0;
    $prix_unitaire = isset($_POST['prix_unitaire']) ? floatval($_POST['prix_unitaire']) : 0;
    
    // Validation basique
    if ($id_article <= 0 || $prix_unitaire <= 0) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit;
    }
    
    try {
        // Préparer la requête de mise à jour
        $sql = "UPDATE article SET prix_unitaire = ? WHERE id = ?";
        $stmt = $connexion->prepare($sql);
        $result = $stmt->execute([$prix_unitaire, $id_article]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Prix mis à jour']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Échec de la mise à jour']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>