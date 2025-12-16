<?php
include 'entete.php';
include '../model/hr_functions.php';

$departements = getDepartements();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organigramme</title>
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

        /* Tree Styles matched to Red Motion Theme */
        .org-container {
            overflow-x: auto;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-soft);
            min-height: 500px;
            text-align: center;
            border: 1px solid var(--border-light);
            /* Custom scrollbar for horizontal scroll */
        }

        .org-container::-webkit-scrollbar {
            height: 8px;
        }
        .org-container::-webkit-scrollbar-thumb {
            background-color: var(--border-medium);
            border-radius: 4px;
        }

        .tree ul {
            padding-top: 20px; 
            position: relative;
            transition: all 0.5s;
            display: flex;
            justify-content: center;
        }

        .tree li {
            float: left; text-align: center;
            list-style-type: none;
            position: relative;
            padding: 20px 5px 0 5px;
            transition: all 0.5s;
        }

        /* Connectors */
        .tree li::before, .tree li::after{
            content: '';
            position: absolute; top: 0; right: 50%;
            border-top: 2px solid var(--border-medium);
            width: 50%; height: 20px;
        }
        .tree li::after{
            right: auto; left: 50%;
            border-left: 2px solid var(--border-medium);
        }

        .tree li:only-child::after, .tree li:only-child::before {
            display: none;
        }

        .tree li:only-child{ padding-top: 0;}

        .tree li:first-child::before, .tree li:last-child::after{
            border: 0 none;
        }

        .tree li:last-child::before{
            border-right: 2px solid var(--border-medium);
            border-radius: 0 5px 0 0;
        }
        .tree li:first-child::after{
            border-radius: 5px 0 0 0;
        }

        .tree ul ul::before{
            content: '';
            position: absolute; top: 0; left: 50%;
            border-left: 2px solid var(--border-medium);
            width: 0; height: 20px;
        }

        /* Nodes */
        .tree li a {
            border: 1px solid var(--border-medium);
            padding: 12px 16px;
            text-decoration: none;
            color: var(--text-medium);
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            display: inline-block;
            border-radius: 12px;
            transition: all 0.3s;
            background: white;
            min-width: 140px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .tree li a:hover, .tree li a:hover+ul li a {
            background: white; 
            color: var(--red-primary); 
            border-color: var(--red-primary);
            box-shadow: 0 4px 12px rgba(230, 57, 70, 0.15);
            transform: translateY(-2px);
        }

        .tree li a:hover+ul li::after, 
        .tree li a:hover+ul li::before, 
        .tree li a:hover+ul::before, 
        .tree li a:hover+ul ul::before{
            border-color: var(--red-light);
        }

        /* Node Types Customization */
        .root-node { 
            background: var(--red-primary) !important; 
            color: white !important; 
            border-color: var(--red-primary) !important; 
            box-shadow: 0 4px 10px rgba(230, 57, 70, 0.3) !important;
        }
        
        .dept-node { 
            background: var(--bg-light) !important; 
            border-top: 4px solid var(--red-dark) !important; 
        }
        
        .pos-node { 
            border-style: dashed !important; 
            background: #fff !important;
        }
        
        .emp-node { 
            display: flex !important; 
            align-items: center; 
            gap: 8px; 
            justify-content: flex-start; 
            cursor: pointer; 
            text-align: left !important;
            padding: 8px 12px !important;
        }

        .node-content { display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .emp-node .node-content { align-items: flex-start; }

        .org-avatar { 
            width: 32px; 
            height: 32px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .org-avatar-placeholder { 
            width: 32px; 
            height: 32px; 
            border-radius: 50%; 
            background: var(--bg-light); 
            color: var(--text-medium); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 12px; 
            font-weight: 700;
            border: 1px solid var(--border-medium);
        }

        /* Modal Overrides */
        .modal-header {
            background: var(--bg-soft);
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-light);
        }
        .modal-title {
            color: var(--text-dark);
        }
        .modal-close {
            color: var(--text-medium);
            background: transparent;
        }
        .modal-close:hover {
            background: var(--bg-light);
            color: var(--red-primary);
        }
        
        /* Fullscreen Mode */
        .org-container.fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            border-radius: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa; /* Ensure background is opaque */
            box-sizing: border-box;
        }
        
        .org-container.fullscreen .tree {
            transform-origin: center center; 
            /* Let JS handle specific scale, but centered */
        }

        /* NEW ORG CONTROLS STYLES */
        .org-controls {
            display: flex;
            gap: 8px;
            background: white;
            padding: 8px;
            border-radius: 12px;
            box-shadow: var(--shadow-medium);
            border: 1px solid var(--border-light);
        }

        .org-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid transparent;
            background: transparent;
            color: var(--text-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .org-btn:hover {
            background: var(--bg-light);
            color: var(--red-primary);
            border-color: var(--border-light);
        }

        .org-btn.active {
            background: var(--red-primary);
            color: white;
            box-shadow: 0 4px 6px rgba(230, 57, 70, 0.2);
        }
    </style>
</head>
<body>

<div class="home-content">
    <div class="header-section">
        <h2 class="page-title">Organigramme</h2>
        <div class="org-controls">
            <button onclick="zoomIn()" class="org-btn" title="Zoom Avant"><i class='bx bx-zoom-in'></i></button>
            <button onclick="zoomOut()" class="org-btn" title="Zoom Arrière"><i class='bx bx-zoom-out'></i></button>
            <button onclick="resetZoom()" class="org-btn" title="Réinitialiser"><i class='bx bx-reset'></i></button>
            <div style="width: 1px; height: 24px; background: var(--border-light); margin: auto 4px;"></div>
            <button onclick="toggleFullScreen()" class="org-btn active" title="Plein Écran"><i class='bx bx-fullscreen'></i></button>
        </div>
    </div>

    <div class="org-container">
        <div class="tree">
            <ul>
                <li>
                    <a href="#" class="root-node">
                        <div class="node-content">
                            <i class='bx bxs-business' style="font-size: 24px;"></i>
                            <strong style="font-size: 14px;">RED MOTION</strong>
                            <span style="font-size: 11px; opacity: 0.9;">Direction Générale</span>
                        </div>
                    </a>
                    <ul>
                        <?php foreach ($departements as $dept): ?>
                            <li>
                                <a href="#" class="dept-node">
                                    <div class="node-content">
                                        <i class='bx bxs-building-house' style="color: var(--red-dark);"></i>
                                        <strong><?= $dept['nom'] ?></strong>
                                    </div>
                                </a>
                                <?php 
                                $postes = getPostes($dept['id']);
                                if (!empty($postes)): 
                                ?>
                                    <ul>
                                        <?php foreach ($postes as $poste): ?>
                                            <li>
                                                <a href="#" class="pos-node">
                                                    <div class="node-content">
                                                        <strong><?= $poste['titre'] ?></strong>
                                                    </div>
                                                </a>
                                                <?php
                                                // Récupérer les employés de ce poste
                                                $sql = "SELECT * FROM employes WHERE id_poste = ?";
                                                $req = $connexion->prepare($sql);
                                                $req->execute([$poste['id']]);
                                                $employes = $req->fetchAll(PDO::FETCH_ASSOC);
                                                
                                                if (!empty($employes)):
                                                ?>
                                                    <ul>
                                                        <?php foreach ($employes as $emp): ?>
                                                            <li>
                                                                <a href="javascript:void(0);" onclick="voirDetails(<?= htmlspecialchars(json_encode($emp)) ?>)" class="emp-node">
                                                                    <?php if ($emp['photo']): ?>
                                                                        <img src="../<?= $emp['photo'] ?>" class="org-avatar">
                                                                    <?php else: ?>
                                                                        <div class="org-avatar-placeholder">
                                                                            <?= strtoupper(substr($emp['nom'], 0, 1)) ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <div style="display: flex; flex-direction: column;">
                                                                        <strong style="font-size: 12px; color: var(--text-dark);"><?= $emp['prenom'] ?> <?= $emp['nom'] ?></strong>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>


<!-- Modal Détails Employé (Using Global Red Motion Styles) -->
<div id="modal-details" class="modal">
    <div class="modal-content" style="max-width: 900px; margin: 50px auto;">
        <div class="modal-header">
            <h3 class="modal-title" style="display: flex; align-items: center; gap: 10px;">
                <span style="background: var(--red-primary); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class='bx bx-id-card'></i>
                </span>
                Fiche Employé
            </h3>
            <button id="btn-fermer-details" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body" style="padding: 0;">
            <div id="details-content">
                <!-- Content will be populated by JS -->
            </div>
        </div>
    </div>
</div>

<script>
    const modalDetails = document.getElementById('modal-details');
    const btnFermerDetails = document.getElementById('btn-fermer-details');

    function fermerModalDetails() {
        modalDetails.style.display = 'none';
    }

    btnFermerDetails.onclick = fermerModalDetails;

    window.onclick = function(event) {
        if (event.target == modalDetails) fermerModalDetails();
    }

    function calculateAge(dateString) {
        if (!dateString) return '';
        const today = new Date();
        const birthDate = new Date(dateString);
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age + ' ans';
    }

    function calculateSeniority(dateString) {
        if (!dateString) return '';
        const today = new Date();
        const startDate = new Date(dateString);
        let years = today.getFullYear() - startDate.getFullYear();
        let months = today.getMonth() - startDate.getMonth();
        if (months < 0 || (months === 0 && today.getDate() < startDate.getDate())) {
            years--;
            months += 12;
        }
        if (years > 0) return years + ' ans et ' + months + ' mois';
        return months + ' mois';
    }

    function voirDetails(data) {
        const content = document.getElementById('details-content');
        
        let photoHtml = data.photo ? `<img src="../${data.photo}" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: var(--shadow-medium);">` : `<div style="width: 120px; height: 120px; border-radius: 50%; background: #eee; display: flex; align-items: center; justify-content: center; border: 5px solid white; box-shadow: var(--shadow-medium);"><i class='bx bx-user' style="font-size: 60px; color: #ccc;"></i></div>`;
        
        let badgeClass = 'badge-success';
        if(data.statut === 'Congé') badgeClass = 'badge-warning';
        if(data.statut === 'Terminé') badgeClass = 'badge-danger';
        
        // Define styles for badge classes manually since they might not be in scope
        const badgeStyles = {
            'badge-success': 'background-color: #D1FAE5; color: #065F46;',
            'badge-warning': 'background-color: #FEF3C7; color: #D97706;',
            'badge-danger': 'background-color: #FEE2E2; color: #991B1B;'
        };

        const age = calculateAge(data.date_naissance);
        const seniority = calculateSeniority(data.date_embauche);

        content.innerHTML = `
            <div style="display: flex; gap: 30px; padding: 30px; align-items: flex-start; flex-wrap: wrap;">
                
                <!-- Left Column: Profile Card -->
                <div style="flex: 0 0 300px; background: white; border-radius: 20px; padding: 30px; box-shadow: var(--shadow-medium); text-align: center; border: 1px solid var(--border-light);">
                    ${photoHtml}
                    <h2 style="margin: 20px 0 5px 0; color: var(--text-dark); font-size: 22px; font-weight: 700;">${data.nom} ${data.prenom}</h2>
                    <div style="color: var(--text-medium); font-size: 14px; margin-bottom: 15px;">${data.poste || 'Poste non défini'}</div>
                    <span style="display: inline-block; padding: 6px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; ${badgeStyles[badgeClass]}">${data.statut}</span>
                    
                    <div style="margin-top: 30px; text-align: left;">
                         <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 12px; color: var(--text-dark); font-size: 14px;">
                            <i class='bx bx-id-card' style="font-size: 18px; color: var(--red-primary);"></i>
                            <span>${data.cin || '-'}</span>
                         </div>
                         <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 12px; color: var(--text-dark); font-size: 14px;">
                            <i class='bx bx-cake' style="font-size: 18px; color: var(--red-primary);"></i>
                            <span>${age} (${data.date_naissance || '-'})</span>
                         </div>
                         <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 12px; color: var(--text-dark); font-size: 14px;">
                            <i class='bx bx-map' style="font-size: 18px; color: var(--red-primary);"></i>
                            <span>${data.adresse || '-'}</span>
                         </div>
                    </div>

                    <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-light);">
                        <a href="imprimer_attestation.php?id=${data.id}" target="_blank" class="btn btn-secondary" style="width: 100%; justify-content: center; margin-bottom: 10px;">
                            <i class='bx bx-printer'></i> Attestation
                        </a>
                    </div>
                </div>

                <!-- Right Column: Detailed Info -->
                <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 20px;">
                    
                    <!-- Professional Info -->
                    <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: var(--shadow-soft); border: 1px solid var(--border-light);">
                        <h4 style="margin: 0 0 20px 0; color: var(--text-dark); font-size: 18px; display: flex; align-items: center; gap: 10px;">
                            <span style="width: 4px; height: 18px; background: var(--red-primary); border-radius: 2px;"></span>
                            Informations Professionnelles
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                             <div>
                                <label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Département</label>
                                <div style="font-size: 15px; color: var(--text-dark); font-weight: 500;">${data.departement || '-'}</div>
                            </div>
                            <div>
                                <label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Type Contrat</label>
                                <div style="font-size: 15px; color: var(--text-dark); font-weight: 500;">${data.type_contrat || '-'}</div>
                            </div>
                            <div>
                                <label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Date Embauche</label>
                                <div style="font-size: 15px; color: var(--text-dark); font-weight: 500;">${data.date_embauche || '-'}</div>
                            </div>
                            <div>
                                <label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Ancienneté</label>
                                <div style="font-size: 15px; color: var(--text-dark); font-weight: 500;">${seniority || '-'}</div>
                            </div>
                            <div>
                                <label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px;">CNSS</label>
                                <div style="font-size: 15px; color: var(--text-dark); font-weight: 500;">${data.cnss || '-'}</div>
                            </div>
                             <div>
                                <label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Salaire de Base</label>
                                <div style="font-size: 15px; color: var(--text-dark); font-weight: 500;">${data.salaire_base ? data.salaire_base + ' MAD' : '-'}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Info -->
                    <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: var(--shadow-soft); border: 1px solid var(--border-light);">
                         <h4 style="margin: 0 0 20px 0; color: var(--text-dark); font-size: 18px; display: flex; align-items: center; gap: 10px;">
                            <span style="width: 4px; height: 18px; background: var(--red-primary); border-radius: 2px;"></span>
                            Autres Informations
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                            <div>
                                <label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Situation Familiale</label>
                                <div style="font-size: 15px; color: var(--text-dark); font-weight: 500;">${data.situation_familiale || '-'}</div>
                            </div>
                            <div>
                                <label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Enfants</label>
                                <div style="font-size: 15px; color: var(--text-dark); font-weight: 500;">${data.nombre_enfants}</div>
                            </div>
                             <div>
                                <label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Email</label>
                                <div style="font-size: 15px; color: var(--text-dark); font-weight: 500;">${data.email || '-'}</div>
                            </div>
                             <div>
                                <label style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Téléphone</label>
                                <div style="font-size: 15px; color: var(--text-dark); font-weight: 500;">${data.telephone || '-'}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        modalDetails.style.display = 'block';
    }
    
    // Zoom & Fullscreen Logic
    let currentZoom = 1;
    const zoomStep = 0.1;
    const orgContainer = document.querySelector('.org-container');
    const tree = document.querySelector('.tree');

    function updateZoom() {
        tree.style.transform = `scale(${currentZoom})`;
        // Adjust origin based on context if needed, usually top-center for tree
        if(!orgContainer.classList.contains('fullscreen')) {
             tree.style.transformOrigin = 'top center'; 
        } else {
             tree.style.transformOrigin = 'center center';
        }
    }

    function zoomIn() {
        currentZoom += zoomStep;
        updateZoom();
    }

    function zoomOut() {
        if (currentZoom > 0.2) {
            currentZoom -= zoomStep;
            updateZoom();
        }
    }

    function resetZoom() {
        currentZoom = 1;
        updateZoom();
    }

    function toggleFullScreen() {
        orgContainer.classList.toggle('fullscreen');
        
        const btn = document.querySelector('button[onclick="toggleFullScreen()"] i');
        if (orgContainer.classList.contains('fullscreen')) {
            btn.classList.remove('bx-fullscreen');
            btn.classList.add('bx-exit-fullscreen');
            // Center the tree nicely
            resetZoom(); 
        } else {
            btn.classList.remove('bx-exit-fullscreen');
            btn.classList.add('bx-fullscreen');
            resetZoom();
        }
    }
    
    // Enable dragging in fullscreen to move around if zoomed in?
    // For now simple scroll / scale is enough. Default container has overflow-x auto.
</script>

<?php include 'pied.php'; ?>
</body>
</html>
