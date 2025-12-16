<?php
session_start();
include __DIR__ . '/../model/hr_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../vue/login.php');
    exit();
}

$id_employe = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Upload Document
    if (isset($_POST['upload_document'])) {
        $titre = htmlspecialchars($_POST['titre']);
        $type = htmlspecialchars($_POST['type']);
        
        if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0) {
            
            $fileExtension = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);
            $fileName = $_FILES['fichier']['name'];
            $fileType = $_FILES['fichier']['type'];
            
            // Allow only certain file types (PDF, Images, Word)
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
            
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                
                // Read file content
                $fileData = file_get_contents($_FILES['fichier']['tmp_name']);
                
                if ($fileData !== false) {
                    if (addDocument([
                        'id_employe' => $id_employe,
                        'titre' => $titre,
                        'type' => $type,
                        'file_data' => $fileData,
                        'mime_type' => $fileType,
                        'file_name' => $fileName
                    ])) {
                        $_SESSION['message'] = ['type' => 'success', 'text' => "Document ajouté avec succès."];
                    } else {
                        $_SESSION['message'] = ['type' => 'danger', 'text' => "Erreur lors de l'enregistrement en base de données."];
                    }
                } else {
                    $_SESSION['message'] = ['type' => 'danger', 'text' => "Erreur lors de la lecture du fichier."];
                }
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => "Type de fichier non autorisé."];
            }
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => "Veuillez sélectionner un fichier valide."];
        }
    }
    
    // Delete Document
    elseif (isset($_POST['delete_document'])) {
        $id_doc = $_POST['id_document'];
        // Ideally verify ownership
        // For now, simpler delete
        if (deleteDocument($id_doc)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => "Document supprimé."];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => "Erreur lors de la suppression."];
        }
    }
}

header('Location: ../vue/profile.php');
exit();
?>
