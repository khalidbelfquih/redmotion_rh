<?php
session_start();
include '../model/function.php';

// Check auth and role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../vue/login.php');
    exit();
}

$connexion = getPDO();
$action = $_REQUEST['action'] ?? '';

try {
    
    // --- SOCIETE INFO ---
    if ($action === 'update_societe') {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $adresse = $_POST['adresse'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        $rc = $_POST['rc'];
        $nif = $_POST['nif'];
        $nis = $_POST['nis'];
        $art = $_POST['art'];
        
        $ice = $_POST['ice'];
        $if_fiscal = $_POST['if_fiscal'];
        $cnss = $_POST['cnss'];
        
        // New Config
        $jours_conge_annuel = $_POST['jours_conge_annuel'] ?? 18;
        $weekend_days = isset($_POST['weekend_days']) ? implode(',', $_POST['weekend_days']) : '0'; // Default Sunday if empty

        // Handle File Uploads
        $uploadDir = '../public/uploads/societe/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $logoPath = null;
        if (!empty($_FILES['logo']['name'])) {
            $logoName = 'logo_' . time() . '_' . basename($_FILES['logo']['name']);
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $logoName)) {
                $logoPath = $uploadDir . $logoName;
            }
        }
        
        $cachetPath = null;
        if (!empty($_FILES['cachet']['name'])) {
            $cachetName = 'cachet_' . time() . '_' . basename($_FILES['cachet']['name']);
            if (move_uploaded_file($_FILES['cachet']['tmp_name'], $uploadDir . $cachetName)) {
                $cachetPath = $uploadDir . $cachetName;
            }
        }
        
        if (!empty($id)) {
            $sql = "UPDATE societe_info SET nom=?, adresse=?, telephone=?, email=?, rc=?, nif=?, nis=?, art=?, ice=?, if_fiscal=?, cnss=?, jours_conge_annuel=?, weekend_days=?";
            $params = [$nom, $adresse, $telephone, $email, $rc, $nif, $nis, $art, $ice, $if_fiscal, $cnss, $jours_conge_annuel, $weekend_days];
            
            if ($logoPath) {
                $sql .= ", logo_path=?";
                $params[] = $logoPath;
            }
            if ($cachetPath) {
                $sql .= ", cachet_path=?";
                $params[] = $cachetPath;
            }
            
            $sql .= " WHERE id=?";
            $params[] = $id;
            
            $stmt = $connexion->prepare($sql);
            $stmt->execute($params);
        } else {
             // Should not happen if init script ran, but just in case
            $sql = "INSERT INTO societe_info (nom, adresse, telephone, email, rc, nif, nis, art, logo_path, cachet_path, jours_conge_annuel, weekend_days) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connexion->prepare($sql);
            $stmt->execute([$nom, $adresse, $telephone, $email, $rc, $nif, $nis, $art, $logoPath, $cachetPath, $jours_conge_annuel, $weekend_days]);
        }
        
        $_SESSION['message'] = ['text' => "Informations mises à jour avec succès", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=societe');
    }
    
    // --- TYPES DE CONGES ---
    elseif ($action === 'save_conge') {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $duree = !empty($_POST['duree']) ? $_POST['duree'] : 0;
        
        if (!empty($id)) {
            $stmt = $connexion->prepare("UPDATE conge_type SET nom=?, description=?, duree=? WHERE id=?");
            $stmt->execute([$nom, $description, $duree, $id]);
        } else {
            $stmt = $connexion->prepare("INSERT INTO conge_type (nom, description, duree) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $description, $duree]);
        }
        
        $_SESSION['message'] = ['text' => "Type de congé enregistré", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=conges');
    }
    elseif ($action === 'delete_conge') {
        $id = $_GET['id'];
        $stmt = $connexion->prepare("DELETE FROM conge_type WHERE id=?");
        $stmt->execute([$id]);
        $_SESSION['message'] = ['text' => "Type de congé supprimé", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=conges');
    }
    
    // --- JOURS FERIES ---
    elseif ($action === 'save_ferie') {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $date = $_POST['date_ferie'];
        $recurrent = isset($_POST['recurrent']) ? 1 : 0;
        
        if (!empty($id)) {
            $stmt = $connexion->prepare("UPDATE jours_feries SET nom=?, date_ferie=?, recurrent=? WHERE id=?");
            $stmt->execute([$nom, $date, $recurrent, $id]);
        } else {
            $stmt = $connexion->prepare("INSERT INTO jours_feries (nom, date_ferie, recurrent) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $date, $recurrent]);
        }
        
        $_SESSION['message'] = ['text' => "Jour férié enregistré", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=feries');
    }
    elseif ($action === 'delete_ferie') {
        $id = $_GET['id'];
        $stmt = $connexion->prepare("DELETE FROM jours_feries WHERE id=?");
        $stmt->execute([$id]);
        $_SESSION['message'] = ['text' => "Jour férié supprimé", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=feries');
    }
    
    // --- TYPES DE POSTES ---
    elseif ($action === 'save_poste') {
        $id = $_POST['id'];
        $titre = $_POST['titre']; // 'nom' in form but 'titre' in DB? Let's check form. Assuming we change form to name='titre' or map here.
        // Previous code used 'nom' and 'poste_type'. Schema shows 'postes' table with 'titre' and 'id_departement'.
        $description = $_POST['description'];
        $id_departement = !empty($_POST['id_departement']) ? $_POST['id_departement'] : null;
        
        if (!empty($id)) {
            $stmt = $connexion->prepare("UPDATE postes SET titre=?, description=?, id_departement=? WHERE id=?");
            $stmt->execute([$titre, $description, $id_departement, $id]);
        } else {
            $stmt = $connexion->prepare("INSERT INTO postes (titre, description, id_departement) VALUES (?, ?, ?)");
            $stmt->execute([$titre, $description, $id_departement]);
        }
        
        $_SESSION['message'] = ['text' => "Poste enregistré", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=postes');
    }
    elseif ($action === 'delete_poste') {
        $id = $_GET['id'];
        $stmt = $connexion->prepare("DELETE FROM postes WHERE id=?");
        $stmt->execute([$id]);
        $_SESSION['message'] = ['text' => "Poste supprimé", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=postes');
    }
    
    // --- DEPARTEMENTS (NEW) ---
    elseif ($action === 'save_departement') {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        
        if (!empty($id)) {
            $stmt = $connexion->prepare("UPDATE departements SET nom=?, description=? WHERE id=?");
            $stmt->execute([$nom, $description, $id]);
        } else {
            $stmt = $connexion->prepare("INSERT INTO departements (nom, description) VALUES (?, ?)");
            $stmt->execute([$nom, $description]);
        }
        
        $_SESSION['message'] = ['text' => "Département enregistré", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=departements');
    }
    elseif ($action === 'delete_departement') {
        $id = $_GET['id'];
        // Check if used in postes or employes before delete?
        // Simple delete for now, foreign keys might restrict or cascade depending on DB setup
        $stmt = $connexion->prepare("DELETE FROM departements WHERE id=?");
        $stmt->execute([$id]);
        $_SESSION['message'] = ['text' => "Département supprimé", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=departements');
    }
    
    // --- TYPES D'EVENEMENTS (NEW) ---
    elseif ($action === 'save_event_type') {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $couleur = $_POST['couleur'];
        $icon = $_POST['icon'];
        
        if (!empty($id)) {
            $stmt = $connexion->prepare("UPDATE event_types SET nom=?, couleur=?, icon=? WHERE id=?");
            $stmt->execute([$nom, $couleur, $icon, $id]);
        } else {
            $stmt = $connexion->prepare("INSERT INTO event_types (nom, couleur, icon) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $couleur, $icon]);
        }
        
        $_SESSION['message'] = ['text' => "Type d'événement enregistré", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=events');
    }
    elseif ($action === 'delete_event_type') {
        $id = $_GET['id'];
        $stmt = $connexion->prepare("DELETE FROM event_types WHERE id=?");
        $stmt->execute([$id]);
        $_SESSION['message'] = ['text' => "Type d'événement supprimé", 'type' => 'success'];
        header('Location: ../vue/parametrage.php?tab=events');
    }
    
    else {
        header('Location: ../vue/parametrage.php');
    }

} catch (PDOException $e) {
    $_SESSION['message'] = ['text' => "Erreur: " . $e->getMessage(), 'type' => 'danger'];
    header('Location: ../vue/parametrage.php');
}
