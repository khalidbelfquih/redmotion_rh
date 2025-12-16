<?php
include 'entete.php';
include '../model/hr_functions.php';

$candidats = getCandidats();

// Calcul des statistiques
$total = count($candidats);
$nouveaux = 0;
$entretiens = 0;
$embauches = 0;

foreach ($candidats as $c) {
    if ($c['statut'] == 'Nouveau') $nouveaux++;
    if ($c['statut'] == 'Entretien') $entretiens++;
    if ($c['statut'] == 'Embauché') $embauches++;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recrutement</title>
    <style>
        .home-content {
            padding: 24px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .page-title {
            color: var(--text-dark);
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            color: white;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-success { background-color: var(--red-primary); }
        .btn-success:hover { background-color: var(--red-dark); }
        
        .btn-secondary { background-color: white; color: var(--text-medium); border: 1px solid var(--border-light); }
        .btn-secondary:hover { background-color: var(--bg-light); }
        
        .btn-primary { background-color: var(--red-primary); }
        .btn-primary:hover { background-color: var(--red-dark); }

        .btn-purple { background-color: #8B5CF6; color: white; }
        .btn-purple:hover { background-color: #7C3AED; }
        
        .search-container {
            background: white;
            padding: 20px; 
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .search-input {
            border: none;
            background: transparent;
            width: 100%;
            outline: none;
            font-size: 14px;
            color: var(--text-dark);
        }

        .summary-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .summary-card {
            flex: 1;
            min-width: 200px;
            background-color: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: var(--shadow-soft);
            text-align: left;
            border: 1px solid var(--border-light);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }
        
        .summary-title {
            color: var(--text-medium);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .summary-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
        }
        
        .data-table-wrapper {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-light);
            overflow: hidden;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background-color: var(--bg-light);
            color: var(--text-medium);
            font-weight: 600;
            text-align: left;
            padding: 16px 24px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            color: var(--text-dark);
            font-size: 14px;
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success { background-color: #D1FAE5; color: #065F46; }
        .badge-warning { background-color: #FEF3C7; color: #D97706; }
        .badge-danger { background-color: #FEE2E2; color: #991B1B; }
        .badge-info { background-color: #DBEAFE; color: #1E40AF; }
        
        .actions-cell {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-medium);
            text-decoration: none;
            transition: all 0.2s;
            background: var(--bg-light);
            border: none;
            cursor: pointer;
        }
        
        .action-btn:hover {
            background: var(--red-primary);
            color: white;
        }

        .user-info { display: flex; align-items: center; gap: 12px; }
        .user-details { display: flex; flex-direction: column; }
        .user-name { font-weight: 600; color: var(--text-dark); }
        .user-email { font-size: 12px; color: var(--text-muted); }
        .user-avatar-placeholder { 
            width: 40px; 
            height: 40px; 
            border-radius: 10px; 
            background: var(--bg-light); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: var(--text-medium);
            font-weight: 700;
        }

        /* Modal Styles override */
        .modal-content { max-width: 600px; }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-medium);
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-medium);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .form-control:focus {
            border-color: var(--red-primary);
            background-color: white;
            outline: none;
            box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
        }

        .btn-link { 
            color: var(--red-primary); 
            text-decoration: none; 
            font-weight: 500; 
            display: inline-flex; 
            align-items: center; 
            gap: 6px;
            padding: 6px 12px;
            background: var(--bg-light);
            border-radius: 6px;
            font-size: 12px;
            transition: all 0.2s;
        }
        .btn-link:hover { background: var(--red-primary); color: white; }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="home-content">
    <div class="header-section">
        <h2 class="page-title">Recrutement</h2>
        <button id="btn-nouveau" class="btn btn-success">
            <i class='bx bx-plus-circle'></i> Nouveau Candidat
        </button>
    </div>
    
    <div class="summary-cards">
        <div class="summary-card" onclick="filterStatus('')">
            <div class="summary-title">Total Candidats</div>
            <div class="summary-value"><?= $total ?></div>
        </div>
        <div class="summary-card" onclick="filterStatus('Nouveau')">
            <div class="summary-title">Nouveaux</div>
            <div class="summary-value" style="color: #1E40AF;"><?= $nouveaux ?></div>
        </div>
        <div class="summary-card" onclick="filterStatus('Entretien')">
            <div class="summary-title">Entretiens</div>
            <div class="summary-value" style="color: #D97706;"><?= $entretiens ?></div>
        </div>
        <div class="summary-card" onclick="filterStatus('Embauché')">
            <div class="summary-title">Embauchés</div>
            <div class="summary-value" style="color: #059669;"><?= $embauches ?></div>
        </div>
    </div>
    
    <div class="search-container">
        <i class='bx bx-search' style="font-size: 1.2rem; color: var(--text-muted);"></i>
        <input type="text" id="searchInput" class="search-input" placeholder="Rechercher par nom, email, téléphone...">
    </div>
    
    <?php if (!empty($_SESSION['message']['text'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 500; background-color: <?= $_SESSION['message']['type'] == 'success' ? '#D1FAE5' : '#FEE2E2' ?>; color: <?= $_SESSION['message']['type'] == 'success' ? '#065F46' : '#991B1B' ?>;">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Candidat</th>
                    <th>Poste Visé</th>
                    <th>Téléphone</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>CV</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php foreach ($candidats as $c): ?>
                    <?php 
                        $statusClass = 'badge-success';
                        if($c['statut'] == 'Nouveau') $statusClass = 'badge-info';
                        if($c['statut'] == 'Entretien') $statusClass = 'badge-warning';
                        if($c['statut'] == 'Rejeté') $statusClass = 'badge-danger';
                    ?>
                    <tr data-status="<?= $c['statut'] ?>">
                        <td>
                            <div class="user-info">
                                <div class="user-avatar-placeholder">
                                    <?= strtoupper(substr($c['prenom'], 0, 1) . substr($c['nom'], 0, 1)) ?>
                                </div>
                                <div class="user-details">
                                    <span class="user-name"><?= $c['nom'] ?> <?= $c['prenom'] ?></span>
                                    <span class="user-email"><?= $c['email'] ?></span>
                                </div>
                            </div>
                        </td>
                        <td><?= $c['poste_vise'] ?></td>
                        <td><?= $c['telephone'] ?></td>
                        <td><?= date('d/m/Y', strtotime($c['date_candidature'])) ?></td>
                        <td><span class="badge <?= $statusClass ?>"><?= $c['statut'] ?></span></td>
                        <td>
                            <?php if ($c['cv_path']): ?>
                                <button onclick="viewerCV('<?= $c['cv_path'] ?>', '<?= htmlspecialchars($c['nom'] . ' ' . $c['prenom']) ?>')" class="btn-link" style="border:none; cursor:pointer; background: transparent;">
                                    <i class='bx bx-show'></i> Visualiser
                                </button>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 12px;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="javascript:void(0);" onclick="voirDetails(<?= htmlspecialchars(json_encode($c)) ?>)" class="action-btn" title="Voir Détails">
                                    <i class='bx bx-show'></i>
                                </a>
                                <a href="javascript:void(0);" onclick="editCandidat(<?= htmlspecialchars(json_encode($c)) ?>)" class="action-btn" title="Modifier">
                                    <i class='bx bx-edit'></i>
                                </a>
                                <a href="javascript:void(0);" onclick="changerStatut(<?= htmlspecialchars(json_encode($c)) ?>)" class="action-btn" style="color: #7C3AED;" title="Changer Statut">
                                    <i class='bx bx-revision'></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout/Modif Candidat -->
<div id="modal-candidat" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-titre">
                <span class='modal-title-icon' style='background:var(--red-primary); color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-user-plus'></i></span>
                Nouveau Candidat
            </h3>
            <button id="btn-fermer-modal" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form id="form-candidat" action="../controller/hr_controller.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add_candidat">
                <input type="hidden" name="id" id="candidatId">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" id="nom" required class="form-control" placeholder="Nom de famille">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" id="prenom" required class="form-control" placeholder="Prénom">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="exemple@email.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control" placeholder="06...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Poste Visé</label>
                        <input type="text" name="poste_vise" id="poste_vise" required class="form-control" placeholder="Ex: Commercial">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Statut</label>
                        <select name="statut" id="statut" class="form-control">
                            <option value="Nouveau">Nouveau</option>
                            <option value="Entretien">Entretien</option>
                            <option value="Embauché">Embauché</option>
                            <option value="Rejeté">Rejeté</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">CV (PDF/Image)</label>
                        <input type="file" name="cv" class="form-control">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Notes ou remarques sur le candidat..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" id="btn-fermer" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Changement Statut Rapide -->
<div id="modal-statut" class="modal">
    <div class="modal-content" style="max-width: 400px; margin-top: 15%;">
        <div class="modal-header">
            <h3 class="modal-title">
                <span class='modal-title-icon' style='background:#7C3AED; color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-revision'></i></span>
                Changer Statut
            </h3>
            <button id="btn-fermer-statut" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form action="../controller/hr_controller.php" method="POST">
                <input type="hidden" name="action" value="quick_update_status">
                <input type="hidden" name="id" id="statutId">
                
                <div class="form-group">
                    <label class="form-label">Nouveau Statut pour <span id="statutNom" style="color:var(--red-primary);"></span></label>
                    <select name="statut" id="statutSelect" class="form-control" style="padding: 12px; font-size: 1rem;">
                        <option value="Nouveau">Nouveau</option>
                        <option value="Entretien">Entretien</option>
                        <option value="Embauché">Embauché</option>
                        <option value="Rejeté">Rejeté</option>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" id="btn-annuler-statut" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Détails Candidat (Redesigned) -->
<div id="modal-details" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3 class="modal-title">
                 <span class='modal-title-icon' style='background:var(--red-primary); color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-id-card'></i></span>
                Fiche Candidat
            </h3>
            <button id="btn-fermer-details" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body" style="padding: 0;">
            <div id="details-content">
                <!-- Populated by JS -->
            </div>
        </div>
    </div>
</div>

<!-- Modal CV Preview (NEW) -->
<div id="modal-cv-preview" class="modal" style="z-index: 2100;">
    <div class="modal-content" style="max-width: 90%; height: 90vh; display: flex; flex-direction: column;">
        <div class="modal-header">
            <h3 class="modal-title" id="cv-preview-title">
                Visualisation CV
            </h3>
            <button id="btn-fermer-cv" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body" style="flex: 1; padding: 0; background: #333; display: flex; justify-content: center; align-items: center; overflow: hidden;" id="cv-preview-container">
            <!-- Iframe or Image will go here -->
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modal-candidat');
    const btnNouveau = document.getElementById('btn-nouveau');
    const btnFermer = document.getElementById('btn-fermer');
    const btnFermerX = document.getElementById('btn-fermer-modal');
    const form = document.getElementById('form-candidat');
    const titreModal = document.getElementById('modal-titre');

    function ouvrirModal() { modal.style.display = 'block'; }
    function fermerModal() { modal.style.display = 'none'; }

    btnNouveau.onclick = function() {
        form.reset();
        document.getElementById('formAction').value = 'add_candidat';
        document.getElementById('candidatId').value = '';
        titreModal.innerHTML = "<span class='modal-title-icon' style='background:var(--red-primary); color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-user-plus'></i></span> Nouveau Candidat";
        ouvrirModal();
    }

    btnFermer.onclick = fermerModal;
    btnFermerX.onclick = fermerModal;

    function editCandidat(c) {
        document.getElementById('formAction').value = 'update_candidat';
        document.getElementById('candidatId').value = c.id;
        titreModal.innerHTML = "<span class='modal-title-icon' style='background:var(--red-primary); color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-edit'></i></span> Modifier Candidat";
        
        document.getElementById('nom').value = c.nom;
        document.getElementById('prenom').value = c.prenom;
        document.getElementById('email').value = c.email;
        document.getElementById('telephone').value = c.telephone;
        document.getElementById('poste_vise').value = c.poste_vise;
        document.getElementById('statut').value = c.statut;
        document.getElementById('notes').value = c.notes;
        
        ouvrirModal();
    }

    // Modal Statut Rapide
    const modalStatut = document.getElementById('modal-statut');
    const btnFermerStatut = document.getElementById('btn-fermer-statut');
    const btnAnnulerStatut = document.getElementById('btn-annuler-statut');

    function fermerModalStatut() { modalStatut.style.display = 'none'; }
    btnFermerStatut.onclick = fermerModalStatut;
    btnAnnulerStatut.onclick = fermerModalStatut;

    function changerStatut(c) {
        document.getElementById('statutId').value = c.id;
        document.getElementById('statutNom').textContent = c.nom + ' ' + c.prenom;
        document.getElementById('statutSelect').value = c.statut;
        modalStatut.style.display = 'block';
    }

    // Modal Détails
    const modalDetails = document.getElementById('modal-details');
    const btnFermerDetails = document.getElementById('btn-fermer-details');

    function fermerModalDetails() { modalDetails.style.display = 'none'; }
    btnFermerDetails.onclick = fermerModalDetails;

    // Modal CV Preview (NEW)
    const modalCV = document.getElementById('modal-cv-preview');
    const btnFermerCV = document.getElementById('btn-fermer-cv');

    function fermerModalCV() { 
        modalCV.style.display = 'none'; 
        document.getElementById('cv-preview-container').innerHTML = ''; // Clear content
    }
    btnFermerCV.onclick = fermerModalCV;

    function viewerCV(path, nom) {
        const container = document.getElementById('cv-preview-container');
        document.getElementById('cv-preview-title').textContent = 'CV - ' + nom;
        
        // Check extension
        const ext = path.split('.').pop().toLowerCase();
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            container.innerHTML = `<img src="../${path}" style="max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 8px; box-shadow: var(--shadow-medium);">`;
        } else {
            // Assume PDF or other browser-supported format
            container.innerHTML = `<iframe src="../${path}" style="width: 100%; height: 80vh; border: none; border-radius: 8px; background: #fdfdfd;"></iframe>`;
        }
        
        modalCV.style.display = 'block';
    }

    window.onclick = function(event) {
        if (event.target == modal) fermerModal();
        if (event.target == modalDetails) fermerModalDetails();
        if (event.target == modalStatut) fermerModalStatut();
        if (event.target == modalCV) fermerModalCV();
    }

    // Badge styling map for JS
    const badgeStyles = {
        'Nouveau': 'background-color: #DBEAFE; color: #1E40AF;',
        'Entretien': 'background-color: #FEF3C7; color: #D97706;',
        'Embauché': 'background-color: #D1FAE5; color: #065F46;',
        'Rejeté': 'background-color: #FEE2E2; color: #991B1B;'
    };

    function voirDetails(data) {
        const content = document.getElementById('details-content');
        const badgeStyle = badgeStyles[data.statut] || 'background-color: #f3f4f6; color: #374151;';

        // Helper for CV button inside details
        const cvButton = data.cv_path 
            ? `<button onclick="viewerCV('${data.cv_path}', '${data.nom} ${data.prenom}')" class="btn-link" style="margin-top:5px; border:none; cursor:pointer;"><i class='bx bx-show'></i> Visualiser le CV</button>` 
            : '<span class="text-muted">Aucun CV</span>';

        content.innerHTML = `
            <div style="position: relative; background: var(--bg-light); padding-bottom: 20px;">
                <div style="height: 100px; background: linear-gradient(135deg, var(--red-primary), 80%, black); border-radius: 0 0 50% 50% / 20px;"></div>
                <div style="margin-top: -50px; display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: var(--shadow-medium); color: var(--text-medium); font-size: 40px; font-weight: 700;">
                        ${data.prenom.charAt(0).toUpperCase()}${data.nom.charAt(0).toUpperCase()}
                    </div>
                    <h2 style="margin: 15px 0 5px 0; color: var(--text-dark); font-size: 24px;">${data.nom} ${data.prenom}</h2>
                    <span style="display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; ${badgeStyle}">${data.statut}</span>
                </div>
            </div>
            
            <div style="padding: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: var(--shadow-soft);">
                    <h4 style="color: var(--red-primary); border-bottom: 1px solid var(--border-light); padding-bottom: 15px; margin-bottom: 20px; font-size: 16px; font-weight: 700; display:flex; align-items:center; gap:8px;"><i class='bx bx-user-pin'></i> Informations Personnelles</h4>
                    <div style="display: grid; gap: 16px;">
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Email</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.email || '-'}</div>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Téléphone</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.telephone || '-'}</div>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Date Candidature</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.date_candidature || '-'}</div>
                        </div>
                    </div>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: var(--shadow-soft);">
                    <h4 style="color: var(--red-primary); border-bottom: 1px solid var(--border-light); padding-bottom: 15px; margin-bottom: 20px; font-size: 16px; font-weight: 700; display:flex; align-items:center; gap:8px;"><i class='bx bx-briefcase'></i> Informations Candidature</h4>
                    <div style="display: grid; gap: 16px;">
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Poste Visé</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.poste_vise || '-'}</div>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">CV</span>
                             <div>
                                ${cvButton}
                             </div>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Notes</span>
                            <div style="font-weight: 400; font-style: italic; color: var(--text-dark); margin-top: 4px;">${data.notes || 'Aucune note'}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        modalDetails.style.display = 'block';
    }

    // Search Functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tableBody tr');
        
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    function filterStatus(status) {
        let rows = document.querySelectorAll('#tableBody tr');
        rows.forEach(row => {
            if (status === '' || row.getAttribute('data-status') === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<?php include 'pied.php'; ?>
</body>
</html>
