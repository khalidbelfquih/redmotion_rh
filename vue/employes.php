<?php
include 'entete.php';
include '../model/hr_functions.php';

// Filtres
$filtres = [
    'recherche' => $_GET['recherche'] ?? '',
    'departement' => $_GET['departement'] ?? '',
    'statut' => $_GET['statut'] ?? ''
];

$employes = getEmployes($filtres);
$departements = getDepartements();
$postes = getPostes();

// Stats pour les cartes
$totalEmployes = count($employes);
$actifs = 0;
$conges = 0;
$termines = 0;

foreach ($employes as $e) {
    if ($e['statut'] == 'Actif') $actifs++;
    if ($e['statut'] == 'Congé') $conges++;
    if ($e['statut'] == 'Terminé') $termines++;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Employés</title>
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
        
        .search-container {
            background: white;
            padding: 20px; 
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-light);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
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
        
        .badge-success { background-color: #D1FAE5; color: #065F46; } /* Green */
        .badge-warning { background-color: #FEF3C7; color: #D97706; } /* Yellow */
        .badge-danger { background-color: #FEE2E2; color: #991B1B; } /* Red */
        
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

        /* Grid for modal form */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        @media (max-width: 992px) {
            .form-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
        }

        .modal-content {
            max-width: 1000px; /* Wider modal for employees */
        }
        
        .filter-group {
            display: flex;
            gap: 12px;
            align-items: center;
            flex: 1;
        }

        .emp-avatar-sm {
            width: 40px; 
            height: 40px; 
            border-radius: 10px; 
            object-fit: cover;
            background-color: var(--bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--text-medium);
        }

    </style>
</head>
<body>

<div class="home-content">
    <div class="header-section">
        <h2 class="page-title">Gestion des Employés</h2>
        <button id="btn-nouveau" class="btn btn-success">
            <i class='bx bx-plus-circle'></i> Nouvel Employé
        </button>
    </div>
    
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-title">Total Employés</div>
            <div class="summary-value"><?= $totalEmployes ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Actifs</div>
            <div class="summary-value" style="color: var(--red-primary);"><?= $actifs ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">En Congé</div>
            <div class="summary-value" style="color: #D97706;"><?= $conges ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Terminés</div>
            <div class="summary-value" style="color: var(--text-muted);"><?= $termines ?></div>
        </div>
    </div>
    
    <?php if (!empty($_SESSION['message']['text'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 500; background-color: <?= $_SESSION['message']['type'] == 'success' ? '#D1FAE5' : '#FEE2E2' ?>; color: <?= $_SESSION['message']['type'] == 'success' ? '#065F46' : '#991B1B' ?>;">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="GET" action="" class="search-container">
        <div class="filter-group" style="flex: 2;">
            <i class='bx bx-search' style="font-size: 1.2rem; color: var(--text-muted);"></i>
            <input type="text" name="recherche" class="search-input" style="border:none; outline:none; font-size:14px; width:100%; color: var(--text-dark);" placeholder="Rechercher par nom, email..." value="<?= htmlspecialchars($filtres['recherche']) ?>">
        </div>
        
        <div class="filter-group">
            <select name="departement" class="form-control" style="width: auto;">
                <option value="">-- Département --</option>
                <?php foreach ($departements as $dept): ?>
                    <option value="<?= $dept['id'] ?>" <?= $filtres['departement'] == $dept['id'] ? 'selected' : '' ?>>
                        <?= $dept['nom'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <select name="statut" class="form-control" style="width: auto;">
                <option value="">-- Statut --</option>
                <option value="Actif" <?= $filtres['statut'] == 'Actif' ? 'selected' : '' ?>>Actif</option>
                <option value="Congé" <?= $filtres['statut'] == 'Congé' ? 'selected' : '' ?>>Congé</option>
                <option value="Terminé" <?= $filtres['statut'] == 'Terminé' ? 'selected' : '' ?>>Terminé</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 10px 16px;">
            Filtrer
        </button>
    </form>
    
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Poste</th>
                    <th>Département</th>
                    <th>Date Embauche</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employes)): ?>
                    <?php foreach ($employes as $emp): ?>
                        <?php
                        $etatClass = 'badge-success';
                        if ($emp['statut'] == 'Congé') $etatClass = 'badge-warning';
                        if ($emp['statut'] == 'Terminé') $etatClass = 'badge-danger';
                        ?>
                        <tr onclick="voirDetails(<?= htmlspecialchars(json_encode($emp)) ?>)" style="cursor: pointer;">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <?php if (!empty($emp['photo'])): ?>
                                        <img src="../<?= $emp['photo'] ?>" class="emp-avatar-sm">
                                    <?php else: ?>
                                        <div class="emp-avatar-sm">
                                            <?= strtoupper(substr($emp['nom'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div style="display: flex; flex-direction: column;">
                                        <span style="font-weight: 600; color: var(--text-dark);"><?= $emp['nom'] ?> <?= $emp['prenom'] ?></span>
                                        <span style="font-size: 12px; color: var(--text-muted);"><?= $emp['email'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?= $emp['poste'] ?></td>
                            <td><?= $emp['departement'] ?></td>
                            <td><?= date('d/m/Y', strtotime($emp['date_embauche'])) ?></td>
                            <td><span class="badge <?= $etatClass ?>"><?= $emp['statut'] ?></span></td>
                            <td>
                                <div class="actions-cell">
                                    <a href="javascript:void(0);" onclick="event.stopPropagation(); voirDetails(<?= htmlspecialchars(json_encode($emp)) ?>)" class="action-btn" title="Voir Détails">
                                        <i class='bx bx-show'></i>
                                    </a>
                                    <a href="javascript:void(0);" onclick="event.stopPropagation(); editEmploye(<?= htmlspecialchars(json_encode($emp)) ?>)" class="action-btn" title="Modifier">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                    <form method="POST" action="../controller/hr_controller.php" style="display:inline;" onsubmit="event.stopPropagation(); return confirm('Supprimer cet employé ?');">
                                        <input type="hidden" name="action" value="delete_employe">
                                        <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                        <button type="submit" class="action-btn" title="Supprimer" onclick="event.stopPropagation();">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding: 40px; color: var(--text-medium);">Aucun employé trouvé</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout/Modif -->
<div id="modal-employe" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-titre" class="modal-title"><i class='bx bx-user-plus'></i> Nouvel Employé</h3>
            <button id="btn-fermer-modal" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form id="form-employe" action="../controller/hr_controller.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add_employe">
                <input type="hidden" name="id" id="employeId">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" id="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" id="prenom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date Naissance</label>
                        <input type="date" name="date_naissance" id="date_naissance" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date Embauche *</label>
                        <input type="date" name="date_embauche" id="date_embauche" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Département *</label>
                        <select id="filter_dept" class="form-control" required onchange="filterPostesByDept()">
                            <option value="">-- Sélectionner Département --</option>
                            <?php foreach ($departements as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Poste *</label>
                        <select name="id_poste" id="id_poste" class="form-control" required disabled>
                            <option value="">-- Sélectionner d'abord le département --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Salaire</label>
                        <input type="number" name="salaire" id="salaire" step="0.01" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Statut</label>
                        <select name="statut" id="statut" class="form-control">
                            <option value="Actif">Actif</option>
                            <option value="Congé">Congé</option>
                            <option value="Terminé">Terminé</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">CIN</label>
                        <input type="text" name="cin" id="cin" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CNSS</label>
                        <input type="text" name="cnss" id="cnss" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Situation Familiale</label>
                        <select name="situation_familiale" id="situation_familiale" class="form-control">
                            <option value="Célibataire">Célibataire</option>
                            <option value="Marié(e)">Marié(e)</option>
                            <option value="Divorcé(e)">Divorcé(e)</option>
                            <option value="Veuf(ve)">Veuf(ve)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Enfants</label>
                        <input type="number" name="nombre_enfants" id="nombre_enfants" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type Contrat</label>
                        <select name="type_contrat" id="type_contrat" class="form-control">
                            <option value="CDI">CDI</option>
                            <option value="CDD">CDD</option>
                            <option value="Anapec">Anapec</option>
                            <option value="Stage">Stage</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">RIB</label>
                        <input type="text" name="rib" id="rib" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Photo</label>
                        <input type="file" name="photo" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Documents (PDF, IMG...)</label>
                        <input type="file" name="documents[]" class="form-control" multiple>
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="form-control">
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

<!-- Modal Détails Employé -->
<div id="modal-details" class="modal">
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h3 class="modal-title"><i class='bx bx-id-card'></i> Fiche Employé</h3>
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
    const modal = document.getElementById('modal-employe');
    const btnNouveau = document.getElementById('btn-nouveau');
    const btnFermer = document.getElementById('btn-fermer');
    const btnFermerX = document.getElementById('btn-fermer-modal');
    const titreModal = document.getElementById('modal-titre');
    const form = document.getElementById('form-employe');
    
    // Inject Posts Data for JS
    const allPostes = <?= json_encode($postes) ?>;
    
    function filterPostesByDept(selectedPosteId = null) {
        const deptId = document.getElementById('filter_dept').value;
        const posteSelect = document.getElementById('id_poste');
        
        posteSelect.innerHTML = '<option value="">-- Sélectionner Poste --</option>';
        
        if (!deptId) {
            posteSelect.disabled = true;
            return;
        }
        
        const filtered = allPostes.filter(p => p.id_departement == deptId);
        
        if (filtered.length > 0) {
            posteSelect.disabled = false;
            filtered.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.titre;
                if (selectedPosteId && p.id == selectedPosteId) {
                    opt.selected = true;
                }
                posteSelect.appendChild(opt);
            });
        } else {
            const opt = document.createElement('option');
            opt.textContent = "Aucun poste dans ce département";
            posteSelect.appendChild(opt);
            posteSelect.disabled = true;
        }
    }

    function ouvrirModal() {
        modal.style.display = 'block';
    }

    function fermerModal() {
        modal.style.display = 'none';
    }

    btnNouveau.onclick = function() {
        form.reset();
        document.getElementById('formAction').value = 'add_employe';
        document.getElementById('employeId').value = '';
        titreModal.innerHTML = "<span class='modal-title-icon' style='background:var(--red-primary); color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-user-plus'></i></span> Nouvel Employé";
        
        // Reset cascading selects
        document.getElementById('filter_dept').value = '';
        document.getElementById('id_poste').innerHTML = '<option value="">-- Sélectionner d\'abord le département --</option>';
        document.getElementById('id_poste').disabled = true;
        
        ouvrirModal();
    }

    btnFermer.onclick = fermerModal;
    btnFermerX.onclick = fermerModal;

    window.onclick = function(event) {
        if (event.target == modal) fermerModal();
        if (event.target == modalDetails) fermerModalDetails();
    }

    function editEmploye(emp) {
        document.getElementById('formAction').value = 'edit_employe';
        document.getElementById('employeId').value = emp.id;
        titreModal.innerHTML = "<span class='modal-title-icon' style='background:var(--red-primary); color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-edit'></i></span> Modifier Employé";
        
        document.getElementById('nom').value = emp.nom;
        document.getElementById('prenom').value = emp.prenom;
        document.getElementById('email').value = emp.email;
        document.getElementById('telephone').value = emp.telephone;
        document.getElementById('date_naissance').value = emp.date_naissance;
        document.getElementById('date_embauche').value = emp.date_embauche;
        
        // Handle cascading select for Edit
        if (emp.id_departement) {
            document.getElementById('filter_dept').value = emp.id_departement;
            filterPostesByDept(emp.id_poste); // Pass current poste ID to select it after filtering
        } else {
             // Fallback if no dept set (legacy data)
             // Try to find dept from allPostes list locally or just show all?
             // Simplest: just load all for that dept if we knew it found via the poste
             // For now let's hope data is consistent
             document.getElementById('filter_dept').value = '';
        }
        
        document.getElementById('salaire').value = emp.salaire;
        document.getElementById('statut').value = emp.statut;
        document.getElementById('cin').value = emp.cin;
        document.getElementById('cnss').value = emp.cnss;
        document.getElementById('adresse').value = emp.adresse;
        document.getElementById('situation_familiale').value = emp.situation_familiale;
        document.getElementById('nombre_enfants').value = emp.nombre_enfants;
        document.getElementById('type_contrat').value = emp.type_contrat;
        document.getElementById('rib').value = emp.rib;
        
        ouvrirModal();
    }

    // Modal Détails
    const modalDetails = document.getElementById('modal-details');
    const btnFermerDetails = document.getElementById('btn-fermer-details');

    function fermerModalDetails() {
        modalDetails.style.display = 'none';
    }

    btnFermerDetails.onclick = fermerModalDetails;

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

        const age = calculateAge(data.date_naissance);
        const seniority = calculateSeniority(data.date_embauche);

        content.innerHTML = `
            <div style="position: relative; background: var(--bg-light); padding-bottom: 20px;">
                <div style="height: 120px; background: linear-gradient(135deg, var(--red-primary), 80%, black); border-radius: 0 0 50% 50% / 20px;"></div>
                <div style="margin-top: -60px; display: flex; flex-direction: column; align-items: center;">
                    ${photoHtml}
                    <h2 style="margin: 15px 0 5px 0; color: var(--text-dark); font-size: 24px;">${data.nom} ${data.prenom}</h2>
                    <span class="badge ${badgeClass}" style="font-size: 14px; padding: 6px 15px;">${data.statut}</span>
                </div>
            </div>
            
            <div style="padding: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: var(--shadow-soft);">
                    <h4 style="color: var(--red-primary); border-bottom: 1px solid var(--border-light); padding-bottom: 15px; margin-bottom: 20px; display:flex; align-items:center; gap:10px;"><i class='bx bx-user-pin'></i> Informations Personnelles</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">CIN</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.cin || '-'}</div>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Date de Naissance</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.date_naissance || '-'}</div>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Situation Familiale</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.situation_familiale || '-'}</div>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Enfants</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.nombre_enfants}</div>
                        </div>
                        <div style="grid-column: 1/-1;">
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Adresse</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.adresse || '-'}</div>
                        </div>
                    </div>
                </div>

                <div style="background: white; padding: 24px; border-radius: 16px; box-shadow: var(--shadow-soft);">
                    <h4 style="color: var(--red-primary); border-bottom: 1px solid var(--border-light); padding-bottom: 15px; margin-bottom: 20px; display:flex; align-items:center; gap:10px;"><i class='bx bx-briefcase'></i> Informations Professionnelles</h4>
                     <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Poste</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.poste || '-'}</div>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Département</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.departement || '-'}</div>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Date Embauche</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.date_embauche || '-'}</div>
                        </div>
                         <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Ancienneté</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${seniority || '-'}</div>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Type Contrat</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.type_contrat || '-'}</div>
                        </div>
                        <div>
                             <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">CNSS</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.cnss || '-'}</div>
                        </div>
                          <div>
                             <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Salaire</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.salaire ? data.salaire + ' DH' : '-'}</div>
                        </div>
                         <div>
                             <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">RIB</span>
                            <div style="font-weight: 600; color: var(--text-dark);">${data.rib || '-'}</div>
                        </div>
                    </div>
                </div>
            <div style="grid-column: 1 / -1; background: white; padding: 24px; border-radius: 16px; box-shadow: var(--shadow-soft);">
                    <h4 style="color: var(--red-primary); border-bottom: 1px solid var(--border-light); padding-bottom: 15px; margin-bottom: 20px; display:flex; align-items:center; gap:10px;"><i class='bx bx-file'></i> Documents</h4>
                    <div style="display: flex; gap: 20px; height: 500px;">
                        <div id="documents-list" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; padding-right: 10px;">
                            <div style="text-align:center; color: var(--text-muted); font-style: italic; padding: 20px;">Chargement des documents...</div>
                        </div>
                        <div id="document-preview-pane" style="flex: 2; border: 1px solid var(--border-light); border-radius: 12px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                            <div style="text-align: center; color: var(--text-muted);">
                                <i class='bx bx-show' style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                                Sélectionner un document pour voir l'aperçu
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        modalDetails.style.display = 'block';

        // Fetch Documents
        fetch(`../controller/api_documents.php?id_employe=${data.id}`)
            .then(response => response.json())
            .then(docs => {
                const docList = document.getElementById('documents-list');
                if (docs && docs.length > 0) {
                    docList.innerHTML = '';
                    docs.forEach(doc => {
                        let icon = 'bx-file-blank';
                        let isPreviewable = false;
                        const type = doc.type.toLowerCase();
                        
                        if (type === 'pdf') {
                            icon = 'bx-file-pdf';
                            isPreviewable = true;
                        }
                        else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(type)) {
                            icon = 'bx-image';
                            isPreviewable = true;
                        }
                        else if (['doc', 'docx'].includes(type)) icon = 'bx-file';

                        // Escape single quotes in title for the onclick handler
                        const safeTitle = doc.titre.replace(/'/g, "\\'");

                        docList.innerHTML += `
                            <div style="background: var(--bg-light); padding: 15px; border-radius: 10px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                                <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                                    <i class='bx ${icon}' style="font-size: 24px; color: var(--red-primary);"></i>
                                    <div style="display: flex; flex-direction: column; overflow: hidden;">
                                        <span style="font-weight: 600; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${doc.titre}">${doc.titre}</span>
                                        <span style="font-size: 11px; color: var(--text-muted);">${doc.type.toUpperCase()}</span>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 5px;">
                                    <button onclick="previewDocument(${doc.id}, '${type}', '${safeTitle}')" class="action-btn" title="Aperçu" style="width: 28px; height: 28px; background: white; border: 1px solid var(--border-medium);">
                                        <i class='bx bx-show'></i>
                                    </button>
                                    <a href="../controller/view_document.php?id=${doc.id}" target="_blank" class="action-btn" title="Télécharger" style="width: 28px; height: 28px;">
                                        <i class='bx bx-download'></i>
                                    </a>
                                     <form method="POST" action="../controller/hr_controller.php" style="display:inline;" onsubmit="return confirm('Confirmer la suppression du document ?');">
                                        <input type="hidden" name="action" value="delete_document">
                                        <input type="hidden" name="id" value="${doc.id}">
                                        <button type="submit" class="action-btn" title="Supprimer" style="width: 28px; height: 28px; background: #fee2e2; color: #dc2626;">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    docList.innerHTML = '<div style="text-align:center; color: var(--text-muted); padding: 20px; background: var(--bg-light); border-radius: 8px;">Aucun document associé</div>';
                }
            })
            .catch(err => {
                console.error("Erreur chargement documents", err);
                document.getElementById('documents-list').innerHTML = '<div style="color:red; text-align:center;">Erreur de chargement des documents.</div>';
            });
    }

    function previewDocument(id, type, title) {
        const pane = document.getElementById('document-preview-pane');
        const url = `../controller/view_document.php?id=${id}`;
        
        pane.innerHTML = `<div style="color: var(--text-medium);"><i class='bx bx-loader-alt bx-spin'></i> Chargement...</div>`;
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(type)) {
            pane.innerHTML = `<img src="${url}&t=${new Date().getTime()}" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;">`;
        } else if (type === 'pdf') {
            const pdfUrl = `${url}&t=${new Date().getTime()}`;
            pane.innerHTML = `
                <object data="${pdfUrl}" type="application/pdf" width="100%" height="100%" style="border-radius: 8px;">
                    <iframe src="${pdfUrl}" width="100%" height="100%" style="border: none;">
                        <p>Votre navigateur ne peut pas afficher ce PDF. <a href="${pdfUrl}">Télécharger le fichier</a></p>
                    </iframe>
                </object>
            `;
        } else {
            pane.innerHTML = `
                <div style="text-align: center; padding: 20px;">
                    <i class='bx bx-file' style="font-size: 48px; color: var(--text-muted); margin-bottom: 10px;"></i>
                    <p style="margin-bottom: 15px;">Cet aperçu n'est pas disponible pour ce type de fichier.</p>
                    <a href="${url}" class="btn btn-primary" target="_blank">Télécharger le fichier</a>
                </div>
            `;
        }
    }
</script>

<?php include 'pied.php'; ?>
</body>
</html>
