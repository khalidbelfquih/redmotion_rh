<?php
session_start();
include 'connexion.php';

// Activer le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Tableau pour la réponse JSON
$response = array('success' => false, 'message' => '', 'id_client' => null);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'ajouter_client') {
        // Récupérer les données du formulaire
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
        $adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
        $date_naissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : null;
        $email = isset($_POST['email']) ? trim($_POST['email']) : null;
        $mutuelle = isset($_POST['mutuelle']) ? trim($_POST['mutuelle']) : null;
        $numero_secu = isset($_POST['numero_secu']) ? trim($_POST['numero_secu']) : null;
        $commentaires = isset($_POST['commentaires']) ? trim($_POST['commentaires']) : null;

        // Validation de base
        if (empty($nom) || empty($prenom) || empty($telephone) || empty($adresse)) {
            $response['message'] = 'Veuillez remplir tous les champs obligatoires.';
            echo json_encode($response);
            exit;
        }

        try {
            // Insérer le nouveau client
            $sql = "INSERT INTO client (nom, prenom, telephone, adresse, date_naissance, email, mutuelle, numero_secu, commentaires) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $req = $connexion->prepare($sql);
            $req->execute(array(
                $nom, 
                $prenom, 
                $telephone, 
                $adresse, 
                $date_naissance, 
                $email, 
                $mutuelle, 
                $numero_secu, 
                $commentaires
            ));
            
            // Récupérer l'ID du client créé
            $id_client = $connexion->lastInsertId();
            
            $response['success'] = true;
            $response['message'] = 'Client ajouté avec succès';
            $response['id_client'] = $id_client;
            
        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
        }
    }
}

// Renvoyer la réponse en JSON
header('Content-Type: application/json');
echo json_encode($response);