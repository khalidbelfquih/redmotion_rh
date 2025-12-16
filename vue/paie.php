<?php
include_once 'entete.php';
include_once '../model/payroll_functions.php';

// Mois et Année par défaut (mois courant)
$mois = isset($_GET['mois']) ? $_GET['mois'] : date('n');
$annee = isset($_GET['annee']) ? $_GET['annee'] : date('Y');

// Liste des mois
$mois_liste = [
    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
    7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
];

// Récupérer les employés avec leur statut de paiement pour la période
$employes = getEmployesAvecStatutPaie($mois, $annee);

// Statistiques
$stats = getStatistiquesPaie($mois, $annee);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de la Paie</title>
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

        .btn-danger { background-color: #EF4444; }
        .btn-danger:hover { background-color: #DC2626; }

        .btn-purple { background-color: #8B5CF6; color: white; }
        .btn-purple:hover { background-color: #7C3AED; }

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
            transition: transform 0.2s;
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
        
        .filter-section {
            background: white;
            padding: 20px; 
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-light);
            display: flex;
            gap: 20px;
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-medium);
            text-transform: uppercase;
        }

        .filter-select {
            padding: 10px 16px;
            border: 1px solid var(--border-medium);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text-dark);
            background-color: white;
            min-width: 150px;
            outline: none;
            cursor: pointer;
        }

        .filter-select:focus {
            border-color: var(--red-primary);
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
        
        .action-history:hover {
            background: #7C3AED;
            color: white;
        }

        .action-pay:hover {
            background: #059669;
            color: white;
        }

        /* Modal Overrides */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border: 1px solid #888;
            max-width: 600px;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: modalSlideUp 0.3s ease-out;
        }

        @keyframes modalSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white; 
            border-radius: 16px 16px 0 0;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 20px 24px;
            background-color: var(--bg-light);
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }

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
            background-color: white;
            color: var(--text-dark);
        }
        
        .form-control:read-only {
            background-color: var(--bg-light);
            color: var(--text-muted);
        }

        .form-control:focus {
            border-color: var(--red-primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
        }

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
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<div class="home-content">
    <div class="header-section">
        <h2 class="page-title">Gestion de la Paie</h2>
    </div>

    <!-- Stats Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-title">Période</div>
            <div class="summary-value" style="font-size: 1.5rem; text-transform: uppercase;"><?= $mois_liste[$mois] ?> <?= $annee ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Total Versé</div>
            <div class="summary-value" style="color: #059669;"><?= number_format($stats['total_verse'] ?? 0, 2) ?> <small style="font-size: 1rem; color: #10B981;">MAD</small></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Avancement Paie</div>
            <div class="summary-value"><?= $stats['nombre_paiements'] ?? 0 ?> <span style="font-size: 1.2rem; color: var(--text-muted); font-weight: 500;">/ <?= count($employes) ?></span></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" style="display: flex; gap: 20px; width: 100%;">
            <div class="filter-group">
                <label class="filter-label">Mois</label>
                <select name="mois" class="filter-select" onchange="this.form.submit()">
                    <?php foreach ($mois_liste as $num => $nom): ?>
                        <option value="<?= $num ?>" <?= $mois == $num ? 'selected' : '' ?>><?= $nom ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Année</label>
                <select name="annee" class="filter-select" onchange="this.form.submit()">
                    <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= $annee == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>" style="padding: 15px; margin-bottom: 24px; border-radius: 8px; font-weight:500; background-color: <?= $_SESSION['message']['type'] == 'success' ? '#D1FAE5' : '#FEE2E2' ?>; color: <?= $_SESSION['message']['type'] == 'success' ? '#065F46' : '#991B1B' ?>;">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Table des employés -->
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Poste</th>
                    <th>Salaire Base</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employes as $emp): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <?php if (!empty($emp['photo'])): ?>
                                    <img src="../<?= $emp['photo'] ?>" alt="" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="user-avatar-placeholder">
                                        <?= strtoupper(substr($emp['prenom'], 0, 1) . substr($emp['nom'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-dark);"><?= $emp['nom'] . ' ' . $emp['prenom'] ?></div>
                                    <div style="font-size: 12px; color: var(--text-muted);"><?= $emp['email'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= $emp['poste'] ?></td>
                        <td style="font-weight: 600;"><?= number_format($emp['salaire'], 2) ?> MAD</td>
                        <td>
                            <?php if ($emp['est_paye']): ?>
                                <span class="badge badge-success">Payé</span>
                            <?php else: ?>
                                <span class="badge badge-warning">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <?php if ($emp['est_paye']): ?>
                                    <a href="imprimer_bulletin.php?id=<?= $emp['id_paiement'] ?>" target="_blank" class="action-btn" title="Imprimer Bulletin">
                                        <i class='bx bx-printer'></i>
                                    </a>
                                <?php else: ?>
                                    <button onclick='openPaymentModal(<?= json_encode($emp) ?>)' class="action-btn action-pay" title="Payer" style="color: #059669;">
                                        <i class='bx bx-money'></i>
                                    </button>
                                <?php endif; ?>
                                <button onclick="viewHistory(<?= $emp['id'] ?>, '<?= addslashes($emp['nom'] . ' ' . $emp['prenom']) ?>')" class="action-btn action-history" title="Historique" style="color: #7C3AED;">
                                    <i class='bx bx-history'></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <span style="background: #059669; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class='bx bx-money'></i>
                </span>
                Nouveau Paiement
            </h3>
            <button class="modal-close" onclick="closeModal('paymentModal')"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form action="../controller/hr_controller.php" method="POST">
                <input type="hidden" name="action" value="confirm_payment">
                <input type="hidden" name="id_employe" id="pay_id_employe">
                <input type="hidden" name="mois" value="<?= $mois ?>">
                <input type="hidden" name="annee" value="<?= $annee ?>">
                
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Employé</label>
                        <input type="text" id="pay_nom_employe" class="form-control" readonly style="font-weight: 600;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Salaire de Base</label>
                        <input type="number" name="salaire_base" id="pay_salaire_base" class="form-control" step="0.01" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Taux Horaire</label>
                        <input type="number" id="pay_taux_horaire" class="form-control" step="0.01">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Heures de Retard</label>
                        <input type="text" id="pay_retard_hours" class="form-control" readonly style="color: #DC2626; font-weight: 600;">
                        <small class="text-muted" id="pay_retard_cost" style="display: block; margin-top: 4px; font-size: 11px;"></small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Heures Sup</label>
                        <input type="text" id="pay_sup_hours" class="form-control" readonly style="color: #059669; font-weight: 600;">
                        <small class="text-muted" id="pay_sup_cost" style="display: block; margin-top: 4px; font-size: 11px;"></small>
                    </div>
                </div>

                <div class="form-grid" style="margin-top: 20px;">
                    <div class="form-group">
                        <label class="form-label">Primes</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" step="0.01" name="primes" id="pay_primes" class="form-control" value="0" oninput="calculateNet()">
                            <button type="button" id="btn_apply_sup" class="btn btn-success" style="padding: 0 12px; background-color: #059669;" title="Appliquer le montant des heures sup" disabled>
                                <i class='bx bx-check'></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Déductions</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" step="0.01" name="deductions" id="pay_deductions" class="form-control" value="0" oninput="calculateNet()">
                            <button type="button" id="btn_apply_deduction" class="btn btn-danger" style="padding: 0 12px;" title="Appliquer le montant du retard" disabled>
                                <i class='bx bx-check'></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-grid" style="margin-top: 24px; background: var(--bg-light); padding: 16px; border-radius: 12px;">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" style="font-weight: 700; color: var(--text-dark); font-size: 1rem;"><i class='bx bx-wallet'></i> Salaire Net à Payer</label>
                        <input type="number" name="salaire_net" id="pay_salaire_net" class="form-control" step="0.01" readonly style="background: white; border: 2px solid #059669; font-weight: 700; color: #059669; font-size: 1.5rem; height: 50px;">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('paymentModal')">Annuler</button>
                    <button type="submit" class="btn btn-success" style="background-color: #059669;">Valider le Paiement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- History Modal -->
<div id="historyModal" class="modal">
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h3 class="modal-title">
                 <span style="background: #7C3AED; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class='bx bx-history'></i>
                </span>
                Historique des Paiements
            </h3>
            <button class="modal-close" onclick="closeModal('historyModal')"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <h4 id="historyEmployeeName" style="margin-bottom: 24px; color: var(--text-dark); padding-bottom: 12px; border-bottom: 1px solid var(--border-light);"></h4>
            <div class="data-table-wrapper">
                <table class="data-table" id="historyTable">
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Date Paiement</th>
                            <th>Salaire Base</th>
                            <th>Primes</th>
                            <th>Déductions</th>
                            <th>Net Payé</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Content loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }

    function openPaymentModal(emp) {
        document.getElementById('pay_id_employe').value = emp.id;
        document.getElementById('pay_nom_employe').value = emp.nom + ' ' + emp.prenom;
        document.getElementById('pay_salaire_base').value = emp.salaire;
        document.getElementById('pay_primes').value = 0;
        document.getElementById('pay_deductions').value = 0;
        
        // Initialize Hourly Rate (Assuming 191h legal base in MA)
        const salary = parseFloat(emp.salaire) || 0;
        const hourlyRate = salary / 191;
        document.getElementById('pay_taux_horaire').value = hourlyRate.toFixed(2);

        // Reset fields
        document.getElementById('pay_retard_hours').value = "0h 0m";
        document.getElementById('pay_retard_cost').textContent = "";
        document.getElementById('btn_apply_deduction').disabled = true;
        
        document.getElementById('pay_sup_hours').value = "0h 0m";
        document.getElementById('pay_sup_cost').textContent = "";
        document.getElementById('btn_apply_sup').disabled = true;

        const mois = document.querySelector('select[name="mois"]').value;
        const annee = document.querySelector('select[name="annee"]').value;

        // Fetch lateness and overtime stats via existing AJAX endpoint
        // Assuming path to AJAX controller is correct from 'vue/' directory
        fetch(`../controller/ajax_payroll.php?action=get_lateness_stats&id_employe=${emp.id}&mois=${mois}&annee=${annee}`)
            .then(response => response.json())
            .then(data => {
                // Lateness Logic
                if (data.total_minutes_retard > 0) {
                    const hours = Math.floor(data.total_minutes_retard / 60);
                    const minutes = data.total_minutes_retard % 60;
                    document.getElementById('pay_retard_hours').value = `${hours}h ${minutes}m`;
                    
                    const hourlyRate = parseFloat(document.getElementById('pay_taux_horaire').value) || 0;
                    const cost = (data.total_minutes_retard / 60) * hourlyRate;
                    
                    document.getElementById('pay_retard_cost').textContent = `Coût estimé: -${cost.toFixed(2)} DH`;
                    
                    const btnApply = document.getElementById('btn_apply_deduction');
                    btnApply.disabled = false;
                    btnApply.onclick = function() {
                        document.getElementById('pay_deductions').value = cost.toFixed(2);
                        calculateNet();
                    };
                } else {
                    document.getElementById('pay_retard_hours').value = "Aucun retard";
                    document.getElementById('pay_retard_cost').textContent = "";
                    document.getElementById('btn_apply_deduction').disabled = true;
                }

                // Overtime Logic
                if (data.total_minutes_sup > 0) {
                    const hours = Math.floor(data.total_minutes_sup / 60);
                    const minutes = data.total_minutes_sup % 60;
                    document.getElementById('pay_sup_hours').value = `${hours}h ${minutes}m`;
                    
                    const hourlyRate = parseFloat(document.getElementById('pay_taux_horaire').value) || 0;
                    const cost = (data.total_minutes_sup / 60) * hourlyRate; // Standard rate, multiplier can be added later
                    
                    document.getElementById('pay_sup_cost').textContent = `Gain estimé: +${cost.toFixed(2)} DH`;
                    
                    const btnApplySup = document.getElementById('btn_apply_sup');
                    btnApplySup.disabled = false;
                    btnApplySup.onclick = function() {
                        document.getElementById('pay_primes').value = cost.toFixed(2);
                        calculateNet();
                    };
                } else {
                    document.getElementById('pay_sup_hours').value = "Aucune heure sup";
                    document.getElementById('pay_sup_cost').textContent = "";
                    document.getElementById('btn_apply_sup').disabled = true;
                }
            })
            .catch(console.error);
        
        calculateNet();
        openModal('paymentModal');
    }

    // Update costs when hourly rate changes manually
    document.getElementById('pay_taux_horaire').addEventListener('input', function() {
        // Only re-calculate if we have data loaded... simplified logic for now:
        // Ideally we would store the minutes in a data attribute to re-calc.
    });

    function calculateNet() {
        const base = parseFloat(document.getElementById('pay_salaire_base').value) || 0;
        const primes = parseFloat(document.getElementById('pay_primes').value) || 0;
        const deductions = parseFloat(document.getElementById('pay_deductions').value) || 0;
        
        const net = base + primes - deductions;
        document.getElementById('pay_salaire_net').value = net.toFixed(2);
    }

    function viewHistory(idEmploye, nomEmploye) {
        document.getElementById('historyEmployeeName').textContent = nomEmploye;
        const tbody = document.querySelector('#historyTable tbody');
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding: 20px;"><i class="bx bx-loader-alt bx-spin" style="font-size: 24px;"></i><br>Chargement...</td></tr>';
        
        openModal('historyModal');

        fetch(`../controller/ajax_payroll.php?action=get_history&id_employe=${idEmploye}`)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding: 20px; color: var(--text-muted);">Aucun historique de paiement trouvé</td></tr>';
                    return;
                }

                const moisNoms = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

                data.forEach(payment => {
                    const row = `
                        <tr>
                            <td><strong>${moisNoms[payment.mois]} ${payment.annee}</strong></td>
                            <td>${new Date(payment.date_paiement).toLocaleDateString('fr-FR')}</td>
                            <td>${parseFloat(payment.salaire_base).toFixed(2)} DH</td>
                            <td style="color: #059669;">+${parseFloat(payment.total_primes).toFixed(2)} DH</td>
                            <td style="color: #EF4444;">-${parseFloat(payment.total_deductions).toFixed(2)} DH</td>
                            <td style="font-weight: bold;">${parseFloat(payment.salaire_net).toFixed(2)} DH</td>
                            <td>
                                <a href="imprimer_bulletin.php?id=${payment.id}" target="_blank" class="action-btn" title="Imprimer">
                                    <i class='bx bx-printer'></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color: #EF4444; padding: 20px;">Erreur lors du chargement des données.</td></tr>';
            });
    }
</script>

<?php include 'pied.php'; ?>
</body>
</html>
