<?php
header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['articles'])) {
        echo json_encode(['success' => false, 'error' => 'Pas de données']);
        exit;
    }
    
    include 'model/connexion.php';
    $db = $connexion ?? $pdo ?? $conn;
    
    if (!$db) {
        echo json_encode(['success' => false, 'error' => 'Pas de connexion']);
        exit;
    }
    
    $articles = $data['articles'];
    $inserted = 0;
    $errors = [];
    
    $db->beginTransaction();
    
    foreach ($articles as $article) {
        if (!isset($article['valid']) || !$article['valid']) continue;
        
        // Vérifier doublon
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM article WHERE reference = ?");
        $checkStmt->execute([$article['reference']]);
        if ($checkStmt->fetchColumn() > 0) {
            $errors[] = "Référence " . $article['reference'] . " existe déjà";
            continue;
        }
        
        // Préparer les données - ATTENTION au type prix_unitaire
        $stmt = $db->prepare("INSERT INTO article (nom_article, marque, modele, reference, couleur, matiere, forme, diametre, emplacement, id_categorie, quantite, prix_unitaire, date_fabrication, date_expiration, images, societe) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $result = $stmt->execute([
            $article['nom_article'] ?? '',
            $article['marque'] ?? '',
            $article['modele'] ?? '',
            $article['reference'] ?? '',
            $article['couleur'] ?? '',
            $article['matiere'] ?? '',
            $article['forme'] ?? '',
            $article['diametre'] ?? '',
            $article['emplacement'] ?? '',
            (int)($article['id_categorie'] ?? 1),
            (int)($article['quantite'] ?? 0),
            (int)($article['prix_unitaire'] ?? 0), // INT pas FLOAT
            $article['date_fabrication'] ?? date('Y-m-d H:i:s'),
            $article['date_expiration'] ?? date('Y-m-d H:i:s', strtotime('+1 year')),
            '',
            $article['societe'] ?? ''
        ]);
        
        if ($result) {
            $inserted++;
        } else {
            $errors[] = "Erreur insertion " . $article['reference'];
        }
    }
    
    if ($inserted > 0) {
        $db->commit();
        echo json_encode([
            'success' => true, 
            'message' => $inserted . ' articles insérés',
            'inserted_count' => $inserted,
            'errors' => $errors
        ]);
    } else {
        $db->rollback();
        echo json_encode([
            'success' => false, 
            'message' => 'Aucun article inséré',
            'errors' => $errors
        ]);
    }
    
} catch (Exception $e) {
    if (isset($db)) $db->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>