<?php
include_once 'entete.php';
include_once '../model/conge_functions.php';
include_once '../model/hr_functions.php';

$employes_list = getEmployes();
$conge_types = getCongeTypes();

// Get leaves based on role
if ($_SESSION['user']['role'] === 'admin') {
    $conges = getConges(); // Admin sees all
} else {
    $conges = getConges(['id_employe' => $_SESSION['user']['id']]); // User sees own
}

// Stats
$totalDemandes = count($conges);
$enAttente = count(array_filter($conges, function($c) { return $c['statut'] == 'En attente'; }));
$approuves = count(array_filter($conges, function($c) { return $c['statut'] == 'Approuvé'; }));
$refuses = count(array_filter($conges, function($c) { return $c['statut'] == 'Refusé'; }));

// Fetch Calendar Settings for Frontend
$stmt = $connexion->query("SELECT weekend_days FROM societe_info LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
$weekendIndices = $settings ? explode(',', $settings['weekend_days']) : [0]; // Default Sunday

$stmt = $connexion->query("SELECT * FROM jours_feries");
$holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);
$jsonHolidays = json_encode($holidays);
$jsonWeekends = json_encode($weekendIndices);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Congés</title>
    
    <!-- Flatpickr for Range Calendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/l10n/fr.js"></script>

    <style>
        /* Flatpickr Red Motion Theme */
        .flatpickr-calendar {
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border: none;
            font-family: 'DM Sans', sans-serif;
            overflow: hidden;
        }
        
        .flatpickr-months {
            background: var(--red-primary) !important;
            border-radius: 16px 16px 0 0;
            padding: 15px 0 5px 0;
            align-items: center;
        }

        .flatpickr-months .flatpickr-month {
            background: transparent !important;
            color: white !important;
            fill: white !important;
            height: 40px;
        }

        .flatpickr-months .flatpickr-prev-month, 
        .flatpickr-months .flatpickr-next-month {
            fill: white !important;
            color: white !important;
            top: 15px; /* Adjust vertical position of arrows */
        }
        
        .flatpickr-months .flatpickr-prev-month:hover svg, 
        .flatpickr-months .flatpickr-next-month:hover svg {
            fill: rgba(255,255,255,0.8) !important;
        }

        .flatpickr-current-month {
            color: white !important;
            padding-top: 0;
            font-size: 110%;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            appearance: none;
            -webkit-appearance: none;
            background: transparent !important;
            font-weight: 700;
        }

        .flatpickr-current-month .numInputWrapper span.arrowUp:after { border-bottom-color: white; }
        .flatpickr-current-month .numInputWrapper span.arrowDown:after { border-top-color: white; }
        
        .flatpickr-weekdays {
            background: var(--red-primary) !important;
            padding-bottom: 5px;
        }
        
        span.flatpickr-weekday {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
        }

        /* Day Selection Styles */
        .flatpickr-day {
            border-radius: 8px !important;
            margin: 2px 0;
            line-height: 38px;
        }

        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, 
        .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, 
        .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, 
        .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover {
            background: var(--red-primary) !important;
            border-color: var(--red-primary) !important;
            color: white !important;
            font-weight: bold;
        }
        
        .flatpickr-day.inRange {
            box-shadow: -5px 0 0 #fee2e2, 5px 0 0 #fee2e2 !important;
            background: #fee2e2 !important; 
            border-color: #fee2e2 !important;
            color: var(--red-dark);
        }

        .flatpickr-day.today {
            border-color: #eee !important;
            position: relative;
        }
        
        .flatpickr-day.today::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background: var(--red-primary);
            border-radius: 50%;
        }

        .flatpickr-day:hover {
            background: #f8f9fa !important;
        }

        /* Weekend & Holiday Styling */
        .flatpickr-day.flatpickr-weekend {
            background-color: #f3f4f6 !important;
            color: #9ca3af !important;
        }

        .flatpickr-day.flatpickr-holiday {
            background-color: #fee2e2 !important;
            color: #ef4444 !important;
            border: 1px dashed #ef4444 !important;
            font-weight: bold;
        }
        
        /* Ensure selection overrides these if needed, or blend them */
        .flatpickr-day.selected.flatpickr-weekend,
        .flatpickr-day.startRange.flatpickr-weekend,
        .flatpickr-day.endRange.flatpickr-weekend,
        .flatpickr-day.selected.flatpickr-holiday,
        .flatpickr-day.startRange.flatpickr-holiday,
        .flatpickr-day.endRange.flatpickr-holiday {
             background: var(--red-primary) !important;
             color: white !important;
             border: none !important;
        }
        
        .flatpickr-day.inRange.flatpickr-weekend,
        .flatpickr-day.inRange.flatpickr-holiday {
            box-shadow: -5px 0 0 #fee2e2, 5px 0 0 #fee2e2 !important;
            background: #fee2e2 !important;
        }
        
    
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
        
        /* Summary Cards */
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
        
        /* Data Table */
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

        .action-approve:hover { background: #059669; color: white; }
        .action-delete:hover { background: #EF4444; color: white; }
        .action-edit:hover { background: #D97706; color: white; }

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
            max-width: 700px;
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

        /* Notifications style override */
        .alert {
            padding: 16px;
            margin-bottom: 24px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            box-shadow: var(--shadow-soft);
            border: none;
        }
        
        .alert-warning { background-color: #FEF3C7; color: #92400E; }
        .alert-success { background-color: #D1FAE5; color: #065F46; }
        .alert-danger { background-color: #FEE2E2; color: #991B1B; }

        /* History Animation */
        @keyframes slideDownFade {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .history-animated {
            animation: slideDownFade 0.5s ease-out;
            border: 1px solid var(--border-medium);
            box-shadow: var(--shadow-soft);
        }

    </style>
</head>
<body>

<div class="home-content">
    <div class="header-section">
        <h2 class="page-title">Gestion des Congés</h2>
        <button onclick="openModal('addCongeModal')" class="btn btn-success" data-tooltip="Créer une nouvelle demande de congé">
            <i class='bx bx-plus-circle'></i> Demander un congé
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-title">Total Demandes</div>
            <div class="summary-value"><?= $totalDemandes ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">En Attente</div>
            <div class="summary-value" style="color: #D97706;"><?= $enAttente ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Approuvés</div>
            <div class="summary-value" style="color: #059669;"><?= $approuves ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Refusés</div>
            <div class="summary-value" style="color: #DC2626;"><?= $refuses ?></div>
        </div>
    </div>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>">
            <i class='bx bx-info-circle' style="font-size: 1.25rem;"></i>
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Notifications -->
    <?php 
    if ($_SESSION['user']['role'] === 'admin') {
        $expiring = getExpiringConges();
        if (!empty($expiring)) {
            foreach ($expiring as $exp) {
                $daysLeft = (strtotime($exp['date_fin']) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
                $msg = $daysLeft == 0 ? "Le congé de <strong>{$exp['nom']} {$exp['prenom']}</strong> se termine aujourd'hui." : "Le congé de <strong>{$exp['nom']} {$exp['prenom']}</strong> se termine demain (1 jour restant).";
                echo "<div class='alert alert-warning'>
                        <i class='bx bx-bell'></i> $msg
                      </div>";
            }
        }
    }
    ?>

    <!-- Leaves List -->
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Période</th>
                    <th>Durée</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($conges)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: var(--text-muted);">Aucune demande de congé trouvée.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($conges as $conge): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <?php if (!empty($conge['photo'])): ?>
                                        <img src="../<?= $conge['photo'] ?>" alt="" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="user-avatar-placeholder">
                                            <?= strtoupper(substr($conge['prenom'], 0, 1) . substr($conge['nom'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div style="font-weight: 600; color: var(--text-dark);"><?= $conge['nom'] . ' ' . $conge['prenom'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span style="font-weight: 500; color: var(--text-dark);"><?= $conge['type_conge'] ?></span></td>
                            <td>
                                <div style="font-size: 13px; color: var(--text-medium);">
                                    <i class='bx bx-calendar-event' style="vertical-align: middle;"></i>
                                    <?= date('d/m/Y', strtotime($conge['date_debut'])) ?>
                                    <i class='bx bx-right-arrow-alt' style="vertical-align: middle;"></i> 
                                    <?= date('d/m/Y', strtotime($conge['date_fin'])) ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                $debut = new DateTime($conge['date_debut']);
                                $fin = new DateTime($conge['date_fin']);
                                echo '<span style="font-weight: 600;">' . ($debut->diff($fin)->days + 1) . ' jours</span>';
                                ?>
                            </td>
                            <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-medium);">
                                <?= $conge['motif'] ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= strtolower(str_replace(' ', '-', $conge['statut'])) == 'en-attente' ? 'warning' : (strtolower($conge['statut']) == 'approuvé' ? 'success' : 'danger') ?>">
                                    <?= $conge['statut'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <?php if ($conge['statut'] === 'Approuvé'): ?>
                                        <a href="imprimer_conge.php?id=<?= $conge['id'] ?>&signed=1" target="_blank" class="action-btn" data-tooltip="Imprimer avec Signature (Signé)" style="color: #059669;">
                                            <i class='bx bx-pen'></i>
                                        </a>
                                        <a href="imprimer_conge.php?id=<?= $conge['id'] ?>&signed=0" target="_blank" class="action-btn" data-tooltip="Imprimer sans Signature">
                                            <i class='bx bx-printer'></i>
                                        </a>
                                        <?php if ($_SESSION['user']['role'] === 'admin' && $conge['employe_statut'] !== 'Actif'): ?>
                                            <button onclick="finishConge(<?= $conge['id'] ?>)" class="action-btn action-approve" data-tooltip="Terminer Congé" style="color: #7C3AED;">
                                                <i class='bx bx-check-double'></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($conge['statut'] === 'En attente'): ?>
                                        <button onclick='editConge(<?= json_encode($conge) ?>)' class="action-btn action-edit" data-tooltip="Modifier la demande" style="color: #D97706;">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <button onclick="deleteConge(<?= $conge['id'] ?>)" class="action-btn action-delete" data-tooltip="Supprimer la demande" style="color: #EF4444;">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($_SESSION['user']['role'] === 'admin' && $conge['statut'] === 'En attente'): ?>
                                        <button onclick="approveConge(<?= $conge['id'] ?>)" class="action-btn action-approve" data-tooltip="Approuver la demande" style="color: #059669;">
                                            <i class='bx bx-check'></i>
                                        </button>
                                        <button onclick="rejectConge(<?= $conge['id'] ?>)" class="action-btn action-delete" data-tooltip="Refuser la demande" style="color: #EF4444;">
                                            <i class='bx bx-x'></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Leave Modal -->
<div id="addCongeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                 <span style="background: var(--red-primary); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class='bx bx-calendar-plus'></i>
                </span>
                Nouvelle Demande
            </h3>
            <button class="modal-close" onclick="closeModal('addCongeModal')"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form action="../controller/conge_controller.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="demande_conge" value="1">
                
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <div id="history-container" style="display: none; background: #fff; padding: 16px; border-radius: 12px; margin-bottom: 20px;">
                            <h5 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 600; color: var(--red-primary);">Historique des congés (Année en cours)</h5>
                            <table style="width: 100%; font-size: 13px; border-collapse: separate; border-spacing: 0 4px;">
                                <thead>
                                    <tr style="text-align: left; color: var(--text-medium);">
                                        <th style="padding: 4px; font-weight: 600;">Type</th>
                                        <th style="padding: 4px; font-weight: 600;">Période</th>
                                        <th style="padding: 4px; font-weight: 600;">Statut</th>
                                    </tr>
                                </thead>
                                <tbody id="history-body">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Employé</label>
                        <select name="id_employe" id="select_employe" class="form-control" required onchange="loadEmployeeHistory(this.value); updateSoldeDisplay();">
                            <?php foreach ($employes_list as $emp): ?>
                                <option value="<?= $emp['id'] ?>" data-solde="<?= $emp['solde_conge'] ?? 0 ?>" <?= $emp['id'] == $_SESSION['user']['id'] ? 'selected' : '' ?>>
                                    <?= $emp['nom'] . ' ' . $emp['prenom'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="solde-display" style="margin-top: 8px; font-size: 13px; color: var(--text-medium); background: #f3f4f6; padding: 6px 10px; border-radius: 6px; display: inline-block;">
                            <!-- Solde will be populated by JS -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Type de Congé</label>
                        <select name="type_conge" class="form-control" required>
                            <option value="">Sélectionner</option>
                            <?php foreach ($conge_types as $type): ?>
                                <option value="<?= htmlspecialchars($type['nom']) ?>"><?= htmlspecialchars($type['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Période du congé</label>
                        <div style="position: relative;">
                            <i class='bx bx-calendar' style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-medium); font-size: 20px; z-index: 10;"></i>
                            <input type="text" id="periode_conge" class="form-control" placeholder="Sélectionnez la période..." style="padding-left: 40px; background-color: white;" required>
                        </div>
                        <small class="text-muted" style="color: var(--text-muted); font-size: 11px; margin-top: 4px; display: block;">Sélectionnez la date de début et de fin</small>
                        
                        <!-- Hidden inputs for backend compatibility -->
                        <input type="hidden" name="date_debut" id="date_debut">
                        <input type="hidden" name="date_fin" id="date_fin">
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Durée (Jours)</label>
                        <input type="text" id="duree" class="form-control" readonly style="background-color: var(--bg-light); font-weight: 600; color: var(--text-dark);">
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Justificatif (Optionnel)</label>
                        <input type="file" name="justificatif" class="form-control">
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Motif</label>
                        <textarea name="motif" class="form-control" rows="3" required placeholder="Raison de la demande..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addCongeModal')">Annuler</button>
                    <button type="submit" class="btn btn-success"><i class='bx bx-send'></i> Soumettre</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Leave Modal -->
<div id="editCongeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                 <span style="background: #D97706; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class='bx bx-edit'></i>
                </span>
                Modifier Demande
            </h3>
            <button class="modal-close" onclick="closeModal('editCongeModal')"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form action="../controller/conge_controller.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_conge" value="1">
                <input type="hidden" name="id_conge" id="edit_id_conge">
                
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Employé</label>
                        <select name="id_employe" id="edit_id_employe" class="form-control" required>
                            <?php foreach ($employes_list as $emp): ?>
                                <option value="<?= $emp['id'] ?>">
                                    <?= $emp['nom'] . ' ' . $emp['prenom'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Type de Congé</label>
                        <select name="type_conge" id="edit_type_conge" class="form-control" required>
                            <option value="">Sélectionner</option>
                            <?php foreach ($conge_types as $type): ?>
                                <option value="<?= htmlspecialchars($type['nom']) ?>"><?= htmlspecialchars($type['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date de Début</label>
                        <input type="date" name="date_debut" id="edit_date_debut" class="form-control" required onchange="calculateDurationEdit()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date de Fin</label>
                        <input type="date" name="date_fin" id="edit_date_fin" class="form-control" required onchange="calculateDurationEdit()">
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Durée (Jours)</label>
                        <input type="text" id="edit_duree" class="form-control" readonly style="background-color: var(--bg-light); font-weight: 600; color: var(--text-dark);">
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Justificatif (Optionnel)</label>
                        <input type="file" name="justificatif" class="form-control">
                        <small class="text-muted" style="margin-top: 4px; display: block; font-size: 12px;">Laisser vide pour conserver le fichier actuel</small>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Motif</label>
                        <textarea name="motif" id="edit_motif" class="form-control" rows="3" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editCongeModal')">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Leave Modal -->
<div id="rejectCongeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                 <span style="background: #EF4444; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class='bx bx-x-circle'></i>
                </span>
                Refuser la demande
            </h3>
            <button class="modal-close" onclick="closeModal('rejectCongeModal')"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form id="rejectForm" action="../controller/conge_controller.php" method="POST">
                <input type="hidden" name="update_statut" value="1">
                <input type="hidden" name="id_conge" id="rejectIdConge">
                <input type="hidden" name="statut" value="Refusé">
                
                <div class="form-group">
                    <label class="form-label">Motif du refus</label>
                    <textarea name="commentaire_admin" id="rejectReason" class="form-control" rows="4" required placeholder="Veuillez indiquer la raison du refus..."></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('rejectCongeModal')">Annuler</button>
                    <button type="submit" class="btn btn-danger">Refuser</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Form (Hidden - For Approval) -->
<form id="statusForm" action="../controller/conge_controller.php" method="POST" style="display: none;">
    <input type="hidden" name="update_statut" value="1">
    <input type="hidden" name="id_conge" id="statusIdConge">
    <input type="hidden" name="statut" id="statusValue">
    <input type="hidden" name="commentaire_admin" id="statusComment">
</form>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" action="../controller/conge_controller.php" method="POST" style="display: none;">
    <input type="hidden" name="delete_conge" value="1">
    <input type="hidden" name="id_conge" id="deleteIdConge">
</form>

<!-- Finish Form (Hidden) -->
<form id="finishForm" action="../controller/conge_controller.php" method="POST" style="display: none;">
    <input type="hidden" name="finish_conge" value="1">
    <input type="hidden" name="id_conge" id="finishIdConge">
</form>

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

    function approveConge(id) {
        if(confirm('Voulez-vous vraiment approuver ce congé ?')) {
            document.getElementById('statusIdConge').value = id;
            document.getElementById('statusValue').value = 'Approuvé';
            document.getElementById('statusForm').submit();
        }
    }

    function finishConge(id) {
        if(confirm('Voulez-vous marquer ce congé comme terminé et réactiver l\'employé ?')) {
            document.getElementById('finishIdConge').value = id;
            document.getElementById('finishForm').submit();
        }
    }

    function rejectConge(id) {
        document.getElementById('rejectIdConge').value = id;
        document.getElementById('rejectReason').value = ''; // Clear previous reason
        openModal('rejectCongeModal');
    }

    function deleteConge(id) {
        if(confirm('Voulez-vous vraiment supprimer cette demande ?')) {
            document.getElementById('deleteIdConge').value = id;
            document.getElementById('deleteForm').submit();
        }
    }

    function editConge(conge) {
        document.getElementById('edit_id_conge').value = conge.id;
        document.getElementById('edit_id_employe').value = conge.id_employe;
        document.getElementById('edit_type_conge').value = conge.type_conge;
        document.getElementById('edit_date_debut').value = conge.date_debut;
        document.getElementById('edit_date_fin').value = conge.date_fin;
        document.getElementById('edit_motif').value = conge.motif;
        
        calculateDurationEdit();
        openModal('editCongeModal');
    }

    function calculateDuration() {
        const start = document.getElementById('date_debut').value;
        const end = document.getElementById('date_fin').value;
        const durationInput = document.getElementById('duree');
        const submitBtn = document.querySelector('#addCongeModal button[type="submit"]');
        
        if (start && end) {
            durationInput.value = 'Calcul...';
            if(submitBtn) submitBtn.disabled = true;
            
            fetch('../controller/api_calculate_leave.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ start: start, end: end })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    // Check against balance (if not Sans Solde/Maladie/etc depending on logic, but request implied strict check)
                    // We assume strict check or at least warning. 
                    const typeConge = document.querySelector('select[name="type_conge"]').value;
                    
                    // Only enforce balance check for "Annuel" usually, but user said "ne doit pas dépassé le reste" (should not exceed remainder)
                    // I will apply it generally or check type. Let's assume strict for now as per request.
                    
                    if (data.days > window.currentSolde && typeConge === 'Annuel') {
                         durationInput.value = data.days + ' jours (Solde insuffisant: ' + window.currentSolde + ')';
                         durationInput.style.color = '#DC2626';
                         durationInput.style.fontWeight = 'bold';
                         // Keep disabled
                    } else {
                        durationInput.value = data.days + ' jours (ouvrables)';
                        durationInput.style.color = 'var(--text-dark)';
                        if(submitBtn) submitBtn.disabled = false;
                    }

                    // Warning if 0 days
                    if (data.days === 0) {
                        if(submitBtn) submitBtn.disabled = true; 
                    }

                } else {
                    durationInput.value = data.message || 'Date invalide';
                    if(submitBtn) submitBtn.disabled = true;
                }
            })
            .catch(err => {
                console.error(err);
                durationInput.value = 'Erreur calcul';
                if(submitBtn) submitBtn.disabled = false; // Fallback
            });
        }
    }

    function calculateDurationEdit() {
        const start = document.getElementById('edit_date_debut').value;
        const end = document.getElementById('edit_date_fin').value;
        const durationInput = document.getElementById('edit_duree');
        
        if (start && end) {
            durationInput.value = 'Calcul...';
            
            fetch('../controller/api_calculate_leave.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ start: start, end: end })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    durationInput.value = data.days + ' jours (ouvrables)';
                } else {
                    durationInput.value = data.message || 'Date invalide';
                }
            })
            .catch(err => {
                console.error(err);
                durationInput.value = 'Erreur calcul';
            });
        }
    }


    function loadEmployeeHistory(id) {
        const container = document.getElementById('history-container');
        const tbody = document.getElementById('history-body');
        const displaySolde = document.getElementById('solde-display');
        
        // Reset animation
        container.classList.remove('history-animated');
        void container.offsetWidth; // Force reflow

        if (!id) {
            container.style.display = 'none';
            if(displaySolde) displaySolde.innerHTML = '';
            return;
        }

        fetch(`../controller/api_conges_employe.php?id_employe=${id}`)
            .then(response => response.json())
            .then(data => {
                // Handling new structure: { history: [], solde: X, ... }
                // Fallback for old API if needed (though we just updated it) checks if data.history exists
                const list = data.history || data; 
                const solde = (data.solde !== undefined) ? data.solde : 0;
                
                // Update Solde Display with Calculated Value
                window.currentSolde = solde;
                if(displaySolde) {
                     let soldeColor = 'var(--text-dark)';
                     if (solde < 5) soldeColor = '#D97706'; 
                     if (solde <= 0) soldeColor = '#DC2626';
                     
                     displaySolde.innerHTML = `Solde restant (Calc. Historique) : <strong style="color: ${soldeColor}">${solde} jours</strong>`;
                }

                // Update Table
                tbody.innerHTML = '';
                if (list.length > 0) {
                    list.forEach(conge => {
                        const row = `
                            <tr style="border-bottom: 1px solid var(--border-light);">
                                <td style="padding: 6px; color: var(--text-dark);">${conge.type_conge}</td>
                                <td style="padding: 6px; color: var(--text-medium); font-size:12px;">${new Date(conge.date_debut).toLocaleDateString('fr-FR')} - ${new Date(conge.date_fin).toLocaleDateString('fr-FR')}</td>
                                <td style="padding: 6px;">
                                    <span class="badge badge-${conge.statut === 'Approuvé' ? 'success' : (conge.statut === 'Refusé' ? 'danger' : 'warning')}" style="font-size: 10px; padding: 2px 8px;">
                                        ${conge.statut}
                                    </span>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="3" style="padding: 10px; text-align: center; color: var(--text-muted); font-size:12px;">Aucun congé cette année.</td></tr>';
                }
                container.style.display = 'block';
                container.classList.add('history-animated');
            })
            .catch(console.error);
    }

    // Initialize Flatpickr for "Nouvelle Demande"
    document.addEventListener('DOMContentLoaded', function() {
        const weekendIndices = <?= $jsonWeekends ?>.map(Number);
        const holidays = <?= $jsonHolidays ?>;
        const selectEmploye = document.getElementById('select_employe');
        
        // Initial Balance Check
        window.currentSolde = 0;
        
        // Remove old static updater, let the fetch handle it
        // We keep this function just in case but empty or redirecting to loadHistory if needed
        // But since onchange calls both, let's remove the redundancy.
        // We will remove 'updateSoldeDisplay' usage from HTML subsequently or make it no-op here.
        
        window.updateSoldeDisplay = function() {
            // Function functionality moved to loadEmployeeHistory
            // This is kept empty to prevent ReferenceError from existing HTML onchange attribute
        };

        // Load History immediately for the pre-selected employee
        if (selectEmploye && selectEmploye.value) {
            loadEmployeeHistory(selectEmploye.value);
        }

        const fp = flatpickr("#periode_conge", {
            mode: "range",
            locale: "fr",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            minDate: "today",
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const date = dayElem.dateObj;
                const dayIndex = date.getDay(); // 0 is Sunday
                
                // Color Weekends
                if (weekendIndices.includes(dayIndex)) {
                    dayElem.classList.add("flatpickr-weekend");
                }

                // Color Holidays
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                const md = `${m}-${d}`;
                const ymd = `${y}-${m}-${d}`;

                let isHoliday = false;
                for (let h of holidays) {
                    if (h.recurrent == 1) {
                        // Check M-D match
                        if (h.date_ferie.endsWith(md)) isHoliday = true;
                    } else {
                        // Check Y-M-D match
                        if (h.date_ferie === ymd) isHoliday = true;
                    }
                }

                if (isHoliday) {
                    dayElem.classList.add("flatpickr-holiday");
                    dayElem.title = "Jour Férié";
                }
            },
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    // Update hidden inputs
                    const start = instance.formatDate(selectedDates[0], "Y-m-d");
                    const end = instance.formatDate(selectedDates[1], "Y-m-d");

                    document.getElementById('date_debut').value = start;
                    document.getElementById('date_fin').value = end;
                    
                    // Display calculated duration
                    calculateDuration();
                }
            }
        });
    });
</script>

<?php include 'pied.php'; ?>
</body>
</html>
