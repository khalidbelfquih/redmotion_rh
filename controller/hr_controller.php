<?php
session_start();
include '../model/hr_functions.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    try {
        if ($action == 'add_employe') {
            // Gestion de l'upload photo
            $photo = '';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                $target_dir = "../uploads/employes/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
                $photo_name = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $photo_name;
                
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    $photo = "uploads/employes/" . $photo_name;
                }
            }
            
            $data = [
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'email' => $_POST['email'],
                'telephone' => $_POST['telephone'],
                'date_naissance' => $_POST['date_naissance'],
                'date_embauche' => $_POST['date_embauche'],
                'salaire' => $_POST['salaire'],
                'id_poste' => $_POST['id_poste'],
                'statut' => $_POST['statut'],
                'adresse' => $_POST['adresse'],
                'cin' => $_POST['cin'],
                'cnss' => $_POST['cnss'],
                'situation_familiale' => $_POST['situation_familiale'],
                'nombre_enfants' => $_POST['nombre_enfants'],
                'type_contrat' => $_POST['type_contrat'],
                'rib' => $_POST['rib'],
                'photo' => $photo
            ];
            
            $id_employe = addEmploye($data);
            
            // Gestion de l'upload documents (DB BLOB)
            if (isset($_FILES['documents'])) {
                $count = count($_FILES['documents']['name']);
                
                for ($i = 0; $i < $count; $i++) {
                    if ($_FILES['documents']['error'][$i] == 0) {
                        $original_name = $_FILES["documents"]["name"][$i];
                        $tmp_name = $_FILES["documents"]["tmp_name"][$i];
                        $file_type = $_FILES["documents"]["type"][$i];
                        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
                        
                        // Read file content
                        $file_content = file_get_contents($tmp_name);
                        
                        $docData = [
                            'id_employe' => $id_employe,
                            'titre' => $original_name,
                            'type' => $file_extension,
                            'file_data' => $file_content,
                            'mime_type' => $file_type,
                            'file_name' => $original_name
                        ];
                        
                        addDocument($docData);
                    }
                }
            }
            $_SESSION['message']['text'] = "Employé ajouté avec succès";
            $_SESSION['message']['type'] = "success";
            header('Location: ../vue/employes.php');
            
        } elseif ($action == 'edit_employe') {
            $id = $_POST['id'];
            
            // Gestion de l'upload photo
            $photo = '';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                $target_dir = "../uploads/employes/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
                $photo_name = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $photo_name;
                
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    $photo = "uploads/employes/" . $photo_name;
                }
            }
            
            $data = [
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'email' => $_POST['email'],
                'telephone' => $_POST['telephone'],
                'date_naissance' => $_POST['date_naissance'],
                'date_embauche' => $_POST['date_embauche'],
                'salaire' => $_POST['salaire'],
                'id_poste' => $_POST['id_poste'],
                'statut' => $_POST['statut'],
                'adresse' => $_POST['adresse'],
                'cin' => $_POST['cin'],
                'cnss' => $_POST['cnss'],
                'situation_familiale' => $_POST['situation_familiale'],
                'nombre_enfants' => $_POST['nombre_enfants'],
                'type_contrat' => $_POST['type_contrat'],
                'rib' => $_POST['rib'],
                'photo' => $photo
            ];
            
            updateEmploye($id, $data);
            
            // Gestion de l'upload documents (DB BLOB) - Ajout de nouveaux documents
            if (isset($_FILES['documents'])) {
                $count = count($_FILES['documents']['name']);
                
                for ($i = 0; $i < $count; $i++) {
                    if ($_FILES['documents']['error'][$i] == 0) {
                        $original_name = $_FILES["documents"]["name"][$i];
                        $tmp_name = $_FILES["documents"]["tmp_name"][$i];
                        $file_type = $_FILES["documents"]["type"][$i];
                        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
                        
                        // Read file content
                        $file_content = file_get_contents($tmp_name);
                        
                        $docData = [
                            'id_employe' => $id,
                            'titre' => $original_name,
                            'type' => $file_extension,
                            'file_data' => $file_content,
                            'mime_type' => $file_type,
                            'file_name' => $original_name
                        ];
                        
                        addDocument($docData);
                    }
                }
            }
            $_SESSION['message']['text'] = "Employé modifié avec succès";
            $_SESSION['message']['type'] = "success";
            header('Location: ../vue/employes.php');
            
        } elseif ($action == 'delete_employe') {
            $id = $_POST['id'];
            deleteEmploye($id);
            $_SESSION['message']['text'] = "Employé supprimé avec succès";
            $_SESSION['message']['type'] = "success";
            header('Location: ../vue/employes.php');
            
        } elseif ($action == 'delete_document') {
            $id = $_POST['id'];
            deleteDocument($id);
            $_SESSION['message']['text'] = "Document supprimé avec succès";
            $_SESSION['message']['type'] = "success";
            header('Location: ../vue/employes.php');
        } elseif ($action == 'add_candidat') {
            // Gestion de l'upload CV
            $cv_path = '';
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
                $target_dir = "../uploads/cv/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["cv"]["name"], PATHINFO_EXTENSION);
                $cv_name = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $cv_name;
                
                if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
                    $cv_path = "uploads/cv/" . $cv_name;
                }
            }
            
            $data = [
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'email' => $_POST['email'],
                'telephone' => $_POST['telephone'],
                'poste_vise' => $_POST['poste_vise'],
                'cv_path' => $cv_path,
                'statut' => 'Nouveau',
                'notes' => $_POST['notes'] ?? ''
            ];
            
            addCandidat($data);
            $_SESSION['message']['text'] = "Candidat ajouté avec succès";
            $_SESSION['message']['type'] = "success";
            header('Location: ../vue/recrutement.php');
            
        } elseif ($action == 'update_candidat') {
            // Gestion de l'upload CV (si nouveau CV)
            $cv_path = '';
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
                $target_dir = "../uploads/cv/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["cv"]["name"], PATHINFO_EXTENSION);
                $cv_name = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $cv_name;
                
                if (move_uploaded_file($_FILES["cv"]["tmp_name"], $target_file)) {
                    $cv_path = "uploads/cv/" . $cv_name;
                }
            }

            $data = [
                'id' => $_POST['id'],
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'email' => $_POST['email'],
                'telephone' => $_POST['telephone'],
                'poste_vise' => $_POST['poste_vise'],
                'statut' => $_POST['statut'],
                'notes' => $_POST['notes'] ?? '',
                'cv_path' => $cv_path
            ];
            
            updateCandidat($data);
            $_SESSION['message']['text'] = "Candidat mis à jour avec succès";
            $_SESSION['message']['type'] = "success";
            header('Location: ../vue/recrutement.php');
            
        } elseif ($action == 'quick_update_status') {
            $id = $_POST['id'];
            $statut = $_POST['statut'];
            updateStatutCandidat($id, $statut);
            $_SESSION['message']['text'] = "Statut modifié avec succès";
            $_SESSION['message']['type'] = "success";
            header('Location: ../vue/recrutement.php');
            $_SESSION['message']['type'] = "success";
            header('Location: ../vue/recrutement.php');
            
        } elseif ($action == 'confirm_payment') {
            include '../model/payroll_functions.php';
            $data = [
                'id_employe' => $_POST['id_employe'],
                'mois' => $_POST['mois'],
                'annee' => $_POST['annee'],
                'salaire_base' => $_POST['salaire_base'],
                'total_primes' => $_POST['total_primes'],
                'total_deductions' => $_POST['total_deductions'],
                'salaire_net' => $_POST['salaire_net']
            ];
            
            if (enregistrerPaiement($data)) {
                $_SESSION['message']['text'] = "Paiement enregistré avec succès";
                $_SESSION['message']['type'] = "success";
            } else {
                $_SESSION['message']['text'] = "Erreur : Paiement déjà effectué pour ce mois";
                $_SESSION['message']['type'] = "danger";
            }
            header('Location: ../vue/paie.php');
        }
        
    } catch (Exception $e) {
        $_SESSION['message']['text'] = "Erreur : " . $e->getMessage();
        $_SESSION['message']['type'] = "danger";
        // Redirect back to the referring page
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: ../vue/employes.php');
        }
    }
}
?>
