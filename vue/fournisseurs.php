<?php
// Créer le fichier vue/fournisseurs.php
include 'entete.php';

// Fonction pour récupérer les fournisseurs
function getFournisseurs($filtres = []) {
    global $connexion;
    
    $sql = "SELECT f.*, 
                   COUNT(c.id) as nb_commandes,
                   COALESCE(SUM(c.total_commande), 0) as total_commandes
            FROM fournisseur f 
            LEFT JOIN commande c ON f.id = c.id_fournisseur 
            WHERE 1=1";
    
    $params = [];
    
    // Appliquer les filtres
    if (!empty($filtres['recherche'])) {
        $recherche = "%{$filtres['recherche']}%";
        $sql .= " AND (f.nom LIKE ? OR f.prenom LIKE ? OR f.societe LIKE ? OR f.telephone LIKE ? OR f.email LIKE ?)";
        $params = array_fill(0, 5, $recherche);
    }
    
    $sql .= " GROUP BY f.id ORDER BY f.nom, f.prenom";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

// Traiter les filtres
$filtres = [];
if (isset($_GET['filtrer'])) {
    if (!empty($_GET['recherche'])) {
        $filtres['recherche'] = $_GET['recherche'];
    }
}

// Récupérer les fournisseurs
$fournisseurs = getFournisseurs($filtres);

// Suppression d'un fournisseur
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id'])) {
    try {
        $id = $_GET['id'];
        
        // Vérifier s'il y a des commandes associées
        $sql_check = "SELECT COUNT(*) as count FROM commande WHERE id_fournisseur = ?";
        $req_check = $connexion->prepare($sql_check);
        $req_check->execute([$id]);
        $result = $req_check->fetch();
        
        if ($result['count'] > 0) {
            $_SESSION['message']['text'] = "Impossible de supprimer ce fournisseur car il a des commandes associées";
            $_SESSION['message']['type'] = "danger";
        } else {
            $sql = "DELETE FROM fournisseur WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$id]);
            
            $_SESSION['message']['text'] = "Fournisseur supprimé avec succès";
            $_SESSION['message']['type'] = "success";
        }
        
        echo "<script>window.location.href = 'fournisseurs.php';</script>";
        exit();
    } catch (Exception $e) {
        $_SESSION['message']['text'] = "Erreur lors de la suppression: " . $e->getMessage();
        $_SESSION['message']['type'] = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des fournisseurs</title>
    <style>
        /* Styles généraux responsive - mêmes que les autres pages */
        :root {
            --primary-color: #0a2558;
            --secondary-color: #2a9d8f;
            --accent-color: #e76f51;
            --light-bg: #f8f9fa;
            --border-color: #ddd;
            --text-color: #333;
            --text-muted: #666;
            --shadow: 0 2px 8px rgba(0,0,0,0.1);
            --border-radius: 8px;
        }
        
        * {
            box-sizing: border-box;
        }
        
        .card {
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-title {
            color: var(--primary-color);
            margin: 0;
            font-size: 1.6rem;
        }
        
        .btn-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            color: white;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn i {
            font-size: 1.2rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
        }
        
        .btn-success {
            background-color: var(--secondary-color);
        }
        
        .btn-danger {
            background-color: var(--accent-color);
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-info {
            background-color: #17a2b8;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .search-form {
            background-color: var(--light-bg);
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.2s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 157, 143, 0.2);
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            background-color: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.2s;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
        }
        
        .summary-title {
            color: var(--text-muted);
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .summary-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .fournisseurs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .fournisseur-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
            transition: all 0.2s ease;
        }
        
        .fournisseur-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        
        .fournisseur-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .fournisseur-name {
            color: var(--primary-color);
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }
        
        .fournisseur-company {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 5px 0;
        }
        
        .fournisseur-contact {
            margin-bottom: 15px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
            color: var(--text-color);
            font-size: 0.9rem;
        }
        
        .contact-item i {
            width: 16px;
            color: var(--secondary-color);
        }
        
        .fournisseur-stats {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-top: 1px solid var(--border-color);
            margin-bottom: 15px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .fournisseur-actions {
            display: flex;
            gap: 5px;
            justify-content: flex-end;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
        }
        
        .action-view {
            background-color: var(--primary-color);
        }
        
        .action-edit {
            background-color: var(--secondary-color);
        }
        
        .action-delete {
            background-color: var(--accent-color);
        }
        
        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin: 20px 0;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: var(--secondary-color);
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: var(--accent-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }
        
        .empty-state-icon {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .empty-state-title {
            color: var(--text-muted);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .empty-state-desc {
            color: var(--text-muted);
            margin-bottom: 20px;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-bg);
        }
        
        .modal-title {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin: 0;
        }
        
        .close {
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            color: var(--text-muted);
        }
        
        .required {
            color: var(--accent-color);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .btn-actions {
                width: 100%;
            }
            
            .btn {
                flex: 1;
                justify-content: center;
            }
            
            .form-group {
                min-width: 100%;
            }
            
            .fournisseurs-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="home-content">
    <!-- En-tête de page -->
    <div class="header-section">
        <h2 class="page-title">Gestion des fournisseurs</h2>
        <div class="btn-actions">
            <button class="btn btn-success" onclick="ouvrirModalFournisseur()">
                <i class='bx bx-plus-circle'></i> Nouveau fournisseur
            </button>
            <a href="commandes.php" class="btn btn-primary">
                <i class='bx bx-arrow-back'></i> Retour aux commandes
            </a>
        </div>
    </div>
    
    <!-- Formulaire de recherche -->
    <div class="card search-form">
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label for="recherche" class="form-label">Rechercher un fournisseur</label>
                    <input type="text" name="recherche" id="recherche" class="form-control" 
                        placeholder="Nom, prénom, société, téléphone, email..." 
                        value="<?= $filtres['recherche'] ?? '' ?>">
                </div>
            </div>
            
            <div class="form-row" style="justify-content: flex-end;">
                <button type="submit" name="filtrer" value="1" class="btn btn-primary">
                    <i class='bx bx-search'></i> Rechercher
                </button>
                <a href="fournisseurs.php" class="btn btn-secondary">
                    <i class='bx bx-reset'></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
    
    <!-- Résumé -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-title">Total fournisseurs</div>
            <div class="summary-value"><?= count($fournisseurs) ?></div>
        </div>
        
        <div class="summary-card">
            <div class="summary-title">Fournisseurs actifs</div>
            <div class="summary-value"><?= count(array_filter($fournisseurs, function($f) { return $f['nb_commandes'] > 0; })) ?></div>
        </div>
    </div>
    
    <!-- Affichage des messages -->
    <?php if (!empty($_SESSION['message']['text'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <!-- Liste des fournisseurs -->
    <?php if (empty($fournisseurs)): ?>
        <div class="card empty-state">
            <div class="empty-state-icon">
                <i class='bx bx-user'></i>
            </div>
            <h3 class="empty-state-title">Aucun fournisseur trouvé</h3>
            <p class="empty-state-desc">Aucun fournisseur ne correspond à vos critères de recherche.</p>
            <button class="btn btn-success" onclick="ouvrirModalFournisseur()">
                <i class='bx bx-plus-circle'></i> Ajouter un fournisseur
            </button>
        </div>
    <?php else: ?>
        <div class="fournisseurs-grid">
            <?php foreach ($fournisseurs as $fournisseur): ?>
                <div class="fournisseur-card">
                    <div class="fournisseur-header">
                        <div>
                            <h3 class="fournisseur-name"><?= $fournisseur['nom'] . ' ' . $fournisseur['prenom'] ?></h3>
                            <?php if (!empty($fournisseur['societe'])): ?>
                                <div class="fournisseur-company"><?= $fournisseur['societe'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="fournisseur-contact">
                        <div class="contact-item">
                            <i class='bx bx-phone'></i>
                            <span><?= $fournisseur['telephone'] ?></span>
                        </div>
                        
                        <?php if (!empty($fournisseur['email'])): ?>
                            <div class="contact-item">
                                <i class='bx bx-envelope'></i>
                                <span><?= $fournisseur['email'] ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="contact-item">
                            <i class='bx bx-map'></i>
                            <span><?= $fournisseur['adresse'] ?></span>
                        </div>
                        
                        <?php if (!empty($fournisseur['site_web'])): ?>
                            <div class="contact-item">
                                <i class='bx bx-globe'></i>
                                <a href="<?= $fournisseur['site_web'] ?>" target="_blank"><?= $fournisseur['site_web'] ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="fournisseur-stats">
                        <div class="stat-item">
                            <div class="stat-value"><?= $fournisseur['nb_commandes'] ?></div>
                            <div class="stat-label">Commandes</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= number_format($fournisseur['total_commandes'], 0, ',', ' ') ?> DH</div>
                            <div class="stat-label">Total</div>
                        </div>
                    </div>
                    
                    <div class="fournisseur-actions">
                        <a href="detail_fournisseur.php?id=<?= $fournisseur['id'] ?>" title="Voir détails" 
                            class="action-btn action-view">
                            <i class='bx bx-show'></i>
                        </a>
                        <a href="modifier_fournisseur.php?id=<?= $fournisseur['id'] ?>" title="Modifier" 
                            class="action-btn action-edit">
                            <i class='bx bx-edit'></i>
                        </a>
                        <a href="javascript:void(0);" onclick="confirmerSuppression(<?= $fournisseur['id'] ?>, '<?= addslashes($fournisseur['nom'] . ' ' . $fournisseur['prenom']) ?>')" 
                            title="Supprimer" class="action-btn action-delete">
                            <i class='bx bx-trash'></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal nouveau fournisseur -->
<div id="modalFournisseur" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Nouveau fournisseur</h3>
            <button type="button" class="close" onclick="fermerModalFournisseur()">&times;</button>
        </div>
        
        <form id="formFournisseur" onsubmit="ajouterFournisseur(event)">
            <div class="form-row">
                <div class="form-group">
                    <label for="modal_nom" class="form-label">Nom <span class="required">*</span></label>
                    <input type="text" id="modal_nom" name="nom" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="modal_prenom" class="form-label">Prénom <span class="required">*</span></label>
                    <input type="text" id="modal_prenom" name="prenom" class="form-control" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="modal_telephone" class="form-label">Téléphone <span class="required">*</span></label>
                    <input type="tel" id="modal_telephone" name="telephone" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="modal_email" class="form-label">Email</label>
                    <input type="email" id="modal_email" name="email" class="form-control">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="modal_adresse" class="form-label">Adresse <span class="required">*</span></label>
                    <input type="text" id="modal_adresse" name="adresse" class="form-control" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="modal_societe" class="form-label">Société</label>
                    <input type="text" id="modal_societe" name="societe" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="modal_ice" class="form-label">ICE</label>
                    <input type="text" id="modal_ice" name="ice" class="form-control">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="modal_site_web" class="form-label">Site web</label>
                    <input type="url" id="modal_site_web" name="site_web" class="form-control">
                </div>
            </div>
            
            <div class="btn-actions" style="justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="fermerModalFournisseur()">
                    Annuler
                </button>
                <button type="submit" class="btn btn-success">
                    <i class='bx bx-check'></i> Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function ouvrirModalFournisseur() {
    document.getElementById('modalFournisseur').style.display = 'block';
}

function fermerModalFournisseur() {
    document.getElementById('modalFournisseur').style.display = 'none';
    document.getElementById('formFournisseur').reset();
}

function ajouterFournisseur(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('ajax/ajouter_fournisseur.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fermerModalFournisseur();
            alert('Fournisseur ajouté avec succès!');
            window.location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'ajout du fournisseur');
    });
}

function confirmerSuppression(id, nom) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le fournisseur "${nom}" ?\n\nAttention: cette action est irréversible.`)) {
        window.location.href = "fournisseurs.php?action=supprimer&id=" + id;
    }
}

// Fermer le modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('modalFournisseur');
    if (event.target === modal) {
        fermerModalFournisseur();
    }
}
</script>

</body>
</html>

<?php include 'pied.php'; ?>