<?php
session_start();
include_once(__DIR__ . '/function.php');

// Fonction pour retourner une réponse JSON
function retournerReponse($success, $message, $redirect = null) {
    // Vérifier si c'est une requête AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message
        ]);
        exit;
    } else {
        // Comportement normal pour les formulaires classiques
        $_SESSION['message'] = [
            'type' => $success ? 'success' : 'danger',
            'text' => $message,
            'context' => 'fournisseur'
        ];
        
        if ($redirect) {
            header("Location: $redirect");
        } else {
            header('Location: ../vue/commande.php');
        }
        exit;
    }
}

// Vérifier si les champs obligatoires sont remplis
if (
    !empty($_POST['nom']) && 
    !empty($_POST['prenom']) && 
    !empty($_POST['telephone']) && 
    !empty($_POST['adresse'])
) {
    // Validation supplémentaire
    $erreurs = [];
    
    // Vérifier la longueur des champs
    if (strlen($_POST['nom']) < 2) {
        $erreurs[] = "Le nom doit contenir au moins 2 caractères";
    }
    
    if (strlen($_POST['prenom']) < 2) {
        $erreurs[] = "Le prénom doit contenir au moins 2 caractères";
    }
    
    if (strlen($_POST['telephone']) < 8) {
        $erreurs[] = "Le numéro de téléphone doit contenir au moins 8 caractères";
    }
    
    // Vérifier l'email si fourni
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse email n'est pas valide";
    }
    
    // Vérifier l'URL du site web si fournie
    if (!empty($_POST['site_web']) && !filter_var($_POST['site_web'], FILTER_VALIDATE_URL)) {
        $erreurs[] = "L'URL du site web n'est pas valide";
    }
    
    if (!empty($erreurs)) {
        retournerReponse(false, implode(', ', $erreurs));
    }
    
    // Nettoyer et préparer les données
    $data = [
        'nom' => trim($_POST['nom']),
        'prenom' => trim($_POST['prenom']),
        'telephone' => trim($_POST['telephone']),
        'adresse' => trim($_POST['adresse']),
        'societe' => !empty($_POST['societe']) ? trim($_POST['societe']) : null,
        'ice' => !empty($_POST['ice']) ? trim($_POST['ice']) : null,
        'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
        'site_web' => !empty($_POST['site_web']) ? trim($_POST['site_web']) : null
    ];
    
    // Vérifier si un fournisseur avec les mêmes données existe déjà
    if (fournisseurExiste($data['nom'], $data['prenom'], $data['telephone'])) {
        retournerReponse(false, 'Un fournisseur avec ce nom, prénom et téléphone existe déjà.');
    }
    
    // Ajouter le fournisseur
    if (ajoutFournisseur($data)) {
        retournerReponse(true, 'Fournisseur ajouté avec succès!');
    } else {
        retournerReponse(false, 'Erreur lors de l\'ajout du fournisseur.');
    }
} else {
    retournerReponse(false, 'Veuillez remplir tous les champs obligatoires (nom, prénom, téléphone, adresse).');
}

/**
 * Vérifier si un fournisseur existe déjà
 */
function fournisseurExiste($nom, $prenom, $telephone) {
    try {
        $pdo = getPDO(); // Utilisez votre fonction de connexion
        
        $sql = "SELECT COUNT(*) FROM fournisseur WHERE nom = :nom AND prenom = :prenom AND telephone = :telephone";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':telephone' => $telephone
        ]);
        
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Fonction getPDO si elle n'existe pas déjà
 */
if (!function_exists('getPDO')) {
    function getPDO() {
        static $pdo = null;
        
        if ($pdo === null) {
            // Remplacez ces valeurs par vos paramètres de connexion
            $host = 'localhost';
            $dbname = 'votre_base_de_donnees';
            $username = 'votre_utilisateur';
            $password = 'votre_mot_de_passe';
            
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
        
        return $pdo;
    }
}
?>