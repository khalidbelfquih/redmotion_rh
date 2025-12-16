<?php
include 'entete.php';
include '../model/chauffeur_functions.php';

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10; 
$offset = ($page - 1) * $limit;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Chauffeurs</title>
    <style>
        /* Styles adaptés de voiture.php */
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
            --transition: all 0.3s ease;
        }
        
        * { box-sizing: border-box; }
        
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
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            color: white;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-primary { background-color: var(--primary-color); }
        .btn-success { background-color: var(--secondary-color); }
        .btn-danger { background-color: var(--accent-color); }
        .btn-secondary { background-color: #6c757d; }
        
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        
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
        
        .summary-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            flex: 1;
            min-width: 200px;
            background-color: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.2s;
        }
        
        .summary-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .data-table-wrapper {
            overflow-x: auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background-color: var(--light-bg);
            color: var(--primary-color);
            font-weight: 600;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid var(--border-color);
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        
        .actions-cell {
            display: flex;
            gap: 8px;
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
            cursor: pointer;
        }
        
        .action-edit { background-color: var(--secondary-color); }
        .action-delete { background-color: var(--accent-color); }
        
        /* Modal Modern Design */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 1000;
            overflow-y: auto;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .modal-content {
            position: relative;
            background-color: #fff;
            max-width: 1100px; /* Increased width */
            width: 95%;
            margin: 30px auto;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            animation: modalSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }
        
        @keyframes modalSlideUp {
            from {opacity: 0; transform: translateY(50px) scale(0.95);}
            to {opacity: 1; transform: translateY(0) scale(1);}
        }
        
        .modal-header {
            padding: 20px 30px;
            background: linear-gradient(135deg, var(--primary-color), #1a237e);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .modal-title {
            font-size: 1.4rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-title i {
            font-size: 1.6rem;
            opacity: 0.9;
        }
        
        .modal-body { 
            padding: 25px; 
            background-color: #f8f9fa;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 columns by default */
            gap: 20px;
        }

        .form-group {
            position: relative;
        }
        
        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            font-weight: 500;
            color: #495057;
            font-size: 0.9rem;
        }

        .form-label i {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: white;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(42, 157, 143, 0.1);
            outline: none;
        }

        .form-control:hover {
            border-color: #ced4da;
        }
        
        .modal-footer {
            padding: 15px 30px;
            background-color: white;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }
        
        .modal-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(90deg);
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .form-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .modal { padding: 10px; }
            .modal-content { margin: 10px auto; }
            .form-grid { grid-template-columns: 1fr; gap: 16px; }
            .modal-header { padding: 16px 20px; }
            .modal-body { padding: 20px; }
        }
    </style>
</head>
<body>

<div class="home-content">
    <div class="header-section">
        <h2 class="page-title">Gestion des Chauffeurs</h2>
        <button id="btn-nouveau-chauffeur" class="btn btn-success">
            <i class='bx bx-plus-circle'></i> Nouveau Chauffeur
        </button>
    </div>
    
    <?php
    $stats = getChauffeursStats();
    ?>
    
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-title">Total Chauffeurs</div>
            <div class="summary-value"><?= $stats['total'] ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Actifs</div>
            <div class="summary-value" style="color: #28a745;"><?= $stats['actif'] ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">En Congé</div>
            <div class="summary-value" style="color: #ffc107;"><?= $stats['conge'] ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Maladie</div>
            <div class="summary-value" style="color: #dc3545;"><?= $stats['maladie'] ?></div>
        </div>
    </div>
    
    <div class="card search-form">
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" value="<?= isset($_GET['nom']) ? $_GET['nom'] : '' ?>">
                </div>
                <div class="form-group">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" value="<?= isset($_GET['prenom']) ? $_GET['prenom'] : '' ?>">
                </div>
                <div class="form-group">
                    <label for="statut" class="form-label">Statut</label>
                    <select name="statut" class="form-control">
                        <option value="">--Tous--</option>
                        <option value="Actif" <?= isset($_GET['statut']) && $_GET['statut'] == 'Actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="En congé" <?= isset($_GET['statut']) && $_GET['statut'] == 'En congé' ? 'selected' : '' ?>>En congé</option>
                        <option value="Maladie" <?= isset($_GET['statut']) && $_GET['statut'] == 'Maladie' ? 'selected' : '' ?>>Maladie</option>
                        <option value="Inactif" <?= isset($_GET['statut']) && $_GET['statut'] == 'Inactif' ? 'selected' : '' ?>>Inactif</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </div>
        </form>
    </div>
    
    <?php if (!empty($_SESSION['message']['text'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 5px; background-color: <?= $_SESSION['message']['type'] == 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message']['type'] == 'success' ? '#155724' : '#721c24' ?>;">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Nom & Prénom</th>
                    <th>Téléphone</th>
                    <th>CNIE</th>
                    <th>Permis</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $chauffeurs = getChauffeur(null, $_GET, $limit, $offset);
                if (!empty($chauffeurs)) {
                    foreach ($chauffeurs as $c) {
                        $etatClass = 'badge-success';
                        if ($c['statut'] == 'En congé') $etatClass = 'badge-warning';
                        if ($c['statut'] == 'Maladie' || $c['statut'] == 'Inactif') $etatClass = 'badge-danger';
                ?>
                <tr>
                    <td>
                        <?php if (!empty($c['photo'])): ?>
                            <img src="<?= $c['photo'] ?>" width="40" height="40" style="object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <i class='bx bx-user-circle' style="font-size: 30px; color: #ccc;"></i>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= $c['nom'] ?> <?= $c['prenom'] ?></strong></td>
                    <td><?= $c['telephone'] ?></td>
                    <td><?= $c['cnie'] ?></td>
                    <td><?= $c['numero_permis'] ?> (<?= $c['categorie_permis'] ?>)</td>
                    <td><span class="badge <?= $etatClass ?>"><?= $c['statut'] ?></span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="javascript:void(0);" onclick="voirDetails(<?= $c['id'] ?>)" class="action-btn action-view" title="Voir Détails" style="background-color: #17a2b8;">
                                <i class='bx bx-show'></i>
                            </a>
                            <a href="javascript:void(0);" onclick="ouvrirModalModification(<?= $c['id'] ?>)" class="action-btn action-edit" title="Modifier">
                                <i class='bx bx-edit'></i>
                            </a>
                            <a href="javascript:void(0);" onclick="confirmerSuppression(<?= $c['id'] ?>)" class="action-btn action-delete" title="Supprimer">
                                <i class='bx bx-trash'></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; padding: 20px;'>Aucun chauffeur trouvé</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout/Modif -->
<div id="modal-chauffeur" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-titre" class="modal-title"><i class='bx bx-user-plus'></i> Ajouter un chauffeur</h3>
            <button id="btn-fermer-modal" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form id="form-chauffeur" action="../controller/ajoutChauffeur.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="chauffeur_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-user'></i> Nom *</label>
                        <input type="text" name="nom" id="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-user'></i> Prénom *</label>
                        <input type="text" name="prenom" id="prenom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-id-card'></i> CNIE *</label>
                        <input type="text" name="cnie" id="cnie" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-phone'></i> Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-phone-call'></i> Tél. Urgence</label>
                        <input type="text" name="telephone_urgence" id="telephone_urgence" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-envelope'></i> Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-calendar'></i> Date Naissance</label>
                        <input type="date" name="date_naissance" id="date_naissance" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-calendar-check'></i> Date Embauche</label>
                        <input type="date" name="date_embauche" id="date_embauche" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-car'></i> N° Permis</label>
                        <input type="text" name="numero_permis" id="numero_permis" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-calendar-x'></i> Exp. Permis</label>
                        <input type="date" name="date_expiration_permis" id="date_expiration_permis" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-list-check'></i> Cat. Permis</label>
                        <select name="categorie_permis" id="categorie_permis" class="form-control">
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-money'></i> Salaire Base</label>
                        <input type="number" name="salaire_base" id="salaire_base" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-info-circle'></i> Statut</label>
                        <select name="statut" id="statut_modal" class="form-control">
                            <option value="Actif">Actif</option>
                            <option value="En congé">En congé</option>
                            <option value="Maladie">Maladie</option>
                            <option value="Inactif">Inactif</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label"><i class='bx bx-map'></i> Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="form-control">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label"><i class='bx bx-text'></i> Observations</label>
                        <textarea name="observations" id="observations" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-image'></i> Photo</label>
                        <input type="file" name="photo" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-file'></i> Papiers (Fichier)</label>
                        <input type="file" name="papier" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-fermer" class="btn btn-secondary"><i class='bx bx-x'></i> Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Détails Chauffeur -->
<div id="modal-details" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #0a2558, #2a9d8f);">
            <h3 class="modal-title"><i class='bx bx-id-card'></i> Fiche Chauffeur</h3>
            <button id="btn-fermer-details" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body" style="padding: 0;">
            <div id="details-content">
                <!-- Content will be populated by JS -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Document Viewer -->
<div id="modal-document-viewer" class="modal" style="z-index: 1100;">
    <div class="modal-content" style="max-width: 90%; height: 90vh; display: flex; flex-direction: column;">
        <div class="modal-header">
            <h3 class="modal-title"><i class='bx bx-file'></i> Visualisation du Document</h3>
            <button onclick="fermerDocument()" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body" style="flex: 1; padding: 0; overflow: hidden; background: #333; display: flex; align-items: center; justify-content: center;">
            <div id="document-container" style="width: 100%; height: 100%;">
                <!-- Document content will be injected here -->
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modal-chauffeur');
    const modalDetails = document.getElementById('modal-details');
    const modalDoc = document.getElementById('modal-document-viewer');
    const form = document.getElementById('form-chauffeur');
    const btnAjout = document.getElementById('btn-nouveau-chauffeur');
    const btnFermer = document.getElementById('btn-fermer');
    const btnFermerX = document.getElementById('btn-fermer-modal');
    const btnFermerDetails = document.getElementById('btn-fermer-details');
    const titreModal = document.getElementById('modal-titre');

    function ouvrirModal() {
        modal.style.display = 'block';
    }

    function fermerModal() {
        modal.style.display = 'none';
    }
    
    function fermerModalDetails() {
        modalDetails.style.display = 'none';
    }

    function fermerDocument() {
        modalDoc.style.display = 'none';
    }

    btnAjout.onclick = function() {
        form.reset();
        document.getElementById('chauffeur_id').value = '';
        form.action = '../controller/ajoutChauffeur.php';
        titreModal.innerHTML = "<i class='bx bx-user-plus'></i> Ajouter un chauffeur";
        ouvrirModal();
    }

    btnFermer.onclick = fermerModal;
    btnFermerX.onclick = fermerModal;
    btnFermerDetails.onclick = fermerModalDetails;
    
    window.onclick = function(event) {
        if (event.target == modal) fermerModal();
        if (event.target == modalDetails) fermerModalDetails();
        if (event.target == modalDoc) fermerDocument();
    }

    function voirDocument(url) {
        const container = document.getElementById('document-container');
        const extension = url.split('.').pop().toLowerCase();
        
        let content = '';
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
            content = `<img src="${url}" style="max-width: 100%; max-height: 100%; object-fit: contain;">`;
        } else if (extension === 'pdf') {
            content = `
                <object data="${url}" type="application/pdf" width="100%" height="100%">
                    <div style="text-align: center; padding-top: 20%; color: white;">
                        <p>Votre navigateur ne supporte pas l'affichage direct des PDF.</p>
                        <a href="${url}" target="_blank" class="btn btn-primary">
                            <i class='bx bx-download'></i> Télécharger le PDF
                        </a>
                    </div>
                </object>
            `;
        } else {
            content = `
                <div style="text-align: center; color: white;">
                    <i class='bx bx-file' style="font-size: 64px; margin-bottom: 20px;"></i>
                    <p>Ce format de fichier ne peut pas être prévisualisé.</p>
                    <a href="${url}" target="_blank" class="btn btn-primary">
                        <i class='bx bx-download'></i> Télécharger le fichier
                    </a>
                </div>
            `;
        }
        
        container.innerHTML = content;
        modalDoc.style.display = 'block';
    }

    function voirDetails(id) {
        fetch(`../model/getChauffeurJson.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                const content = document.getElementById('details-content');
                
                let photoHtml = data.photo ? `<img src="${data.photo}" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">` : `<div style="width: 120px; height: 120px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2);"><i class='bx bx-user' style="font-size: 60px; color: #ccc;"></i></div>`;
                
                // Updated to use voirDocument
                let papierLink = data.papier ? `
                    <button onclick="voirDocument('${data.papier}')" class="btn btn-primary btn-sm" style="margin-top: 10px;">
                        <i class='bx bx-show'></i> Voir Documents
                    </button>` : '';
                
                let badgeClass = 'badge-success';
                if(data.statut === 'En congé') badgeClass = 'badge-warning';
                if(data.statut === 'Maladie' || data.statut === 'Inactif') badgeClass = 'badge-danger';

                content.innerHTML = `
                    <div style="position: relative; background: #f8f9fa; padding-bottom: 20px;">
                        <div style="height: 100px; background: linear-gradient(135deg, #0a2558, #2a9d8f); border-radius: 0 0 50% 50% / 20px;"></div>
                        <div style="margin-top: -60px; display: flex; flex-direction: column; align-items: center;">
                            ${photoHtml}
                            <h2 style="margin: 15px 0 5px 0; color: #0a2558;">${data.nom} ${data.prenom}</h2>
                            <span class="badge ${badgeClass}" style="font-size: 14px; padding: 6px 15px;">${data.statut}</span>
                        </div>
                    </div>
                    
                    <div style="padding: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                        <div>
                            <h4 style="color: #2a9d8f; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;"><i class='bx bx-user-pin'></i> Informations Personnelles</h4>
                            <p style="margin-bottom: 10px;"><strong><i class='bx bx-id-card'></i> CNIE:</strong> ${data.cnie}</p>
                            <p style="margin-bottom: 10px;"><strong><i class='bx bx-cake'></i> Né(e) le:</strong> ${data.date_naissance || '-'}</p>
                            <p style="margin-bottom: 10px;"><strong><i class='bx bx-map'></i> Adresse:</strong> ${data.adresse || '-'}</p>
                            <p style="margin-bottom: 10px;"><strong><i class='bx bx-phone'></i> Tél:</strong> ${data.telephone || '-'}</p>
                            <p style="margin-bottom: 10px;"><strong><i class='bx bx-envelope'></i> Email:</strong> ${data.email || '-'}</p>
                        </div>
                        
                        <div>
                            <h4 style="color: #2a9d8f; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;"><i class='bx bx-briefcase'></i> Informations Pro</h4>
                            <p style="margin-bottom: 10px;"><strong><i class='bx bx-calendar-check'></i> Embauché(e) le:</strong> ${data.date_embauche || '-'}</p>
                            <p style="margin-bottom: 10px;"><strong><i class='bx bx-car'></i> Permis:</strong> ${data.numero_permis || '-'} (${data.categorie_permis || '-'})</p>
                            <p style="margin-bottom: 10px;"><strong><i class='bx bx-time'></i> Exp. Permis:</strong> ${data.date_expiration_permis || '-'}</p>
                            <p style="margin-bottom: 10px;"><strong><i class='bx bx-money'></i> Salaire:</strong> ${data.salaire_base ? data.salaire_base + ' DH' : '-'}</p>
                            <p style="margin-bottom: 10px;"><strong><i class='bx bx-phone-call'></i> Urgence:</strong> ${data.telephone_urgence || '-'}</p>
                        </div>
                    </div>
                    
                    ${data.observations ? `
                    <div style="padding: 0 30px 30px 30px;">
                        <h4 style="color: #2a9d8f; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;"><i class='bx bx-note'></i> Observations</h4>
                        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; color: #856404;">
                            ${data.observations}
                        </div>
                    </div>` : ''}
                    
                    <div style="padding: 0 30px 30px 30px; text-align: center;">
                        ${papierLink}
                    </div>
                `;
                
                modalDetails.style.display = 'block';
            });
    }

    function ouvrirModalModification(id) {
        fetch(`../model/getChauffeurJson.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                // Remplir le formulaire
                document.getElementById('chauffeur_id').value = data.id;
                document.getElementById('nom').value = data.nom;
                document.getElementById('prenom').value = data.prenom;
                document.getElementById('cnie').value = data.cnie;
                document.getElementById('telephone').value = data.telephone;
                document.getElementById('telephone_urgence').value = data.telephone_urgence;
                document.getElementById('email').value = data.email;
                document.getElementById('adresse').value = data.adresse;
                document.getElementById('date_naissance').value = data.date_naissance;
                document.getElementById('date_embauche').value = data.date_embauche;
                document.getElementById('numero_permis').value = data.numero_permis;
                document.getElementById('date_expiration_permis').value = data.date_expiration_permis;
                document.getElementById('categorie_permis').value = data.categorie_permis;
                document.getElementById('salaire_base').value = data.salaire_base;
                document.getElementById('statut_modal').value = data.statut;
                document.getElementById('observations').value = data.observations;

                // Mettre à jour l'action et le titre
                form.action = '../controller/modifChauffeur.php';
                titreModal.innerHTML = "<i class='bx bx-edit'></i> Modifier le chauffeur";
                
                ouvrirModal();
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert("Erreur lors du chargement des données du chauffeur");
            });
    }

    function confirmerSuppression(id) {
        if (confirm('Voulez-vous vraiment supprimer ce chauffeur ?')) {
            window.location.href = `../controller/supprimerChauffeur.php?id=${id}`;
        }
    }
</script>

<?php include 'pied.php'; ?>
</body>
</html>
