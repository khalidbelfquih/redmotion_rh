<?php
include 'entete.php';
require_once '../model/retard_functions.php';
require_once '../model/heures_sup_functions.php';
require_once '../model/absence_functions.php';
require_once '../model/hr_functions.php';

$mois = date('m');
$annee = date('Y');
$stats = getRetardStats($mois, $annee);
$retards = getRetards(['mois' => $mois, 'annee' => $annee]);
$heures_sup = getHeuresSup(['mois' => $mois, 'annee' => $annee]);
$stats_sup = getHeureSupStats($mois, $annee);
$absences = getAbsences(['mois' => $mois, 'annee' => $annee]);
$stats_absence = getAbsenceStats($mois, $annee);
$employes = getEmployes();

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'retards';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Temps</title>
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
        
        .page-subtitle {
            color: var(--text-medium); 
            margin-top: 5px; 
            font-size: 1rem;
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
        
        .btn-success { background-color: var(--green-primary, #059669); }
        .btn-success:hover { background-color: var(--green-dark, #047857); }
        
        .btn-secondary { background-color: white; color: var(--text-medium); border: 1px solid var(--border-light); }
        .btn-secondary:hover { background-color: var(--bg-light); }
        
        .btn-primary { background-color: var(--red-primary); }
        .btn-primary:hover { background-color: var(--red-dark); }
        
        .btn-danger { background-color: var(--red-primary); }
        .btn-danger:hover { background-color: var(--red-dark); }

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
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid var(--border-light);
        }
        
        .summary-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .summary-info h3 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--text-dark);
            font-weight: 700;
        }
        
        .summary-info p {
            margin: 4px 0 0;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
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

        .action-delete:hover {
            background: #EF4444;
            color: white;
        }
        
        /* Tabs Styled */
        .tabs {
            display: flex;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-light);
            gap: 20px;
        }
        
        .tab-btn {
            padding: 12px 0;
            border: none;
            background: none;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .tab-btn:hover {
            color: var(--red-primary);
        }
        
        .tab-btn.active {
            color: var(--red-primary);
            border-bottom-color: var(--red-primary);
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
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
            width: 500px;
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
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: var(--text-muted);
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background-color: var(--bg-light);
            color: var(--text-dark);
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

        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-medium);
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-medium);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
            background-color: white;
            color: var(--text-dark);
        }

        .form-control:focus {
            border-color: var(--red-primary);
            box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
            outline: none;
        }

        .employee-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .employee-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background-color: var(--bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--text-medium);
            overflow: hidden;
            font-size: 12px;
        }

        .employee-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="home-content">
    <div class="header-section">
        <div>
            <h2 class="page-title">Gestion des Temps</h2>
            <p class="page-subtitle"><?= date('F Y') ?></p>
        </div>
    </div>

    <div class="tabs">
        <button class="tab-btn <?= $active_tab == 'retards' ? 'active' : '' ?>" onclick="switchTab('retards')">
            <i class='bx bx-timer'></i> Retards
        </button>
        <button class="tab-btn <?= $active_tab == 'heures_sup' ? 'active' : '' ?>" onclick="switchTab('heures_sup')">
            <i class='bx bx-up-arrow-circle'></i> Heures Supplémentaires
        </button>
        <button class="tab-btn <?= $active_tab == 'absence' ? 'active' : '' ?>" onclick="switchTab('absence')">
            <i class='bx bx-calendar-x'></i> Suivi Absence
        </button>
    </div>

    <!-- TAB RETARDS -->
    <div id="tab-retards" class="tab-content <?= $active_tab == 'retards' ? 'active' : '' ?>">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
            <button onclick="openModal('addRetardModal')" class="btn btn-primary">
                <i class='bx bx-plus-circle'></i> Nouveau Retard
            </button>
        </div>

        <!-- Summary Cards Retards -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #FEF3C7; color: #D97706;">
                    <i class='bx bx-timer'></i>
                </div>
                <div class="summary-info">
                    <h3><?= $stats['total_retards'] ?></h3>
                    <p>Retards ce mois</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #FEE2E2; color: #991B1B;">
                    <i class='bx bx-error'></i>
                </div>
                <div class="summary-info">
                    <h3><?= count($stats['employes_risk']) ?></h3>
                    <p>Employés à risque (3+)</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #ECFDF5; color: #059669;">
                    <i class='bx bx-time-five'></i>
                </div>
                <div class="summary-info">
                    <h3><?= $stats['total_minutes'] ?? 0 ?> min</h3>
                    <p>Temps perdu total</p>
                </div>
            </div>
        </div>

        <!-- Risk Alert Section -->
        <?php if (!empty($stats['employes_risk'])): ?>
        <div class="data-table-wrapper" style="margin-bottom: 32px; border-left: 4px solid #EF4444;">
            <div style="padding: 16px 24px; border-bottom: 1px solid var(--border-light); display: flex; align-items: center; gap: 10px;">
                <i class='bx bx-alarm-exclamation' style="color: #EF4444; font-size: 1.5rem;"></i>
                <h3 style="margin: 0; color: #EF4444; font-size: 16px; font-weight: 700;">Avertissements Nécessaires</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Retards Injustifiés</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['employes_risk'] as $risk): ?>
                    <tr>
                        <td style="font-weight: 600;"><?= $risk['nom'] . ' ' . $risk['prenom'] ?></td>
                        <td><span class="badge badge-danger"><?= $risk['nb_retards'] ?></span></td>
                        <td>
                            <a href="imprimer_avertissement.php?id_employe=<?= $risk['id_employe'] ?>" target="_blank" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px; background-color: white; color: #EF4444; border: 1px solid #EF4444;">
                                <i class='bx bx-printer'></i> Imprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Retards List -->
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Date</th>
                        <th>Durée</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($retards as $retard): ?>
                    <tr>
                        <td>
                            <div class="employee-cell">
                                <div class="employee-avatar">
                                    <?php if ($retard['photo']): ?>
                                        <img src="../<?= $retard['photo'] ?>" alt="Photo">
                                    <?php else: ?>
                                        <span><?= substr($retard['nom'], 0, 1) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600;"><?= $retard['nom'] . ' ' . $retard['prenom'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= date('d/m/Y', strtotime($retard['date_retard'])) ?></td>
                        <td><span style="font-weight: 600;"><?= $retard['duree_minutes'] ?> min</span></td>
                        <td style="color: var(--text-muted); font-style: italic;"><?= $retard['motif'] ?></td>
                        <td>
                            <?php if ($retard['justifie']): ?>
                                <span class="badge badge-success">Justifié</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Injustifié</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <form action="../controller/retard_controller.php" method="POST" onsubmit="return confirm('Supprimer ce retard ?');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_retard">
                                    <input type="hidden" name="id" value="<?= $retard['id'] ?>">
                                    <button type="submit" class="action-btn action-delete" title="Supprimer"><i class='bx bx-trash'></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB HEURES SUP -->
    <div id="tab-heures_sup" class="tab-content <?= $active_tab == 'heures_sup' ? 'active' : '' ?>">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
            <button onclick="openModal('addHeureSupModal')" class="btn btn-success">
                <i class='bx bx-plus-circle'></i> Nouvelle Heure Sup
            </button>
        </div>

        <!-- Summary Cards Heures Sup -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #ECFDF5; color: #047857;">
                    <i class='bx bx-up-arrow-circle'></i>
                </div>
                <div class="summary-info">
                    <h3><?= $stats_sup['total_count'] ?></h3>
                    <p>Enregistrements</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #DBEAFE; color: #1E40AF;">
                    <i class='bx bx-time'></i>
                </div>
                <div class="summary-info">
                    <h3><?= floor($stats_sup['total_minutes'] / 60) ?>h <?= $stats_sup['total_minutes'] % 60 ?>m</h3>
                    <p>Total Heures Sup</p>
                </div>
            </div>
        </div>

        <!-- Heures Sup List -->
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Date</th>
                        <th>Durée</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($heures_sup as $hs): ?>
                    <tr>
                        <td>
                            <div class="employee-cell">
                                <div class="employee-avatar">
                                    <?php if ($hs['photo']): ?>
                                        <img src="../<?= $hs['photo'] ?>" alt="Photo">
                                    <?php else: ?>
                                        <span><?= substr($hs['nom'], 0, 1) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600;"><?= $hs['nom'] . ' ' . $hs['prenom'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= date('d/m/Y', strtotime($hs['date_heure'])) ?></td>
                        <td><span style="font-weight: 600; color: #059669;"><?= $hs['duree_minutes'] ?> min</span></td>
                        <td style="color: var(--text-muted); font-style: italic;"><?= $hs['motif'] ?></td>
                        <td>
                            <?php if ($hs['valide']): ?>
                                <span class="badge badge-success">Validé</span>
                            <?php else: ?>
                                <span class="badge badge-warning">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <form action="../controller/heures_sup_controller.php" method="POST" onsubmit="return confirm('Supprimer cette heure sup ?');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_heure_sup">
                                    <input type="hidden" name="id" value="<?= $hs['id'] ?>">
                                    <button type="submit" class="action-btn action-delete" title="Supprimer"><i class='bx bx-trash'></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB ABSENCE -->
    <div id="tab-absence" class="tab-content <?= $active_tab == 'absence' ? 'active' : '' ?>">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
            <button onclick="openModal('addAbsenceModal')" class="btn btn-primary">
                <i class='bx bx-plus-circle'></i> Nouvelle Absence
            </button>
        </div>

        <!-- Summary Cards Absence -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #FEE2E2; color: #7f1d1d;">
                    <i class='bx bx-calendar-x'></i>
                </div>
                <div class="summary-info">
                    <h3><?= $stats_absence['total_absences'] ?></h3>
                    <p>Absences ce mois</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #FEF3C7; color: #D97706;">
                    <i class='bx bx-time-five'></i>
                </div>
                <div class="summary-info">
                    <h3><?= $stats_absence['total_days'] ?></h3>
                    <p>Jours perdus</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="background-color: #F3F4F6; color: #4B5563;">
                    <i class='bx bx-error-circle'></i>
                </div>
                <div class="summary-info">
                    <h3><?= $stats_absence['unjustified_count'] ?></h3>
                    <p>Non Justifiées</p>
                </div>
            </div>
        </div>

        <!-- Absence List -->
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Période</th>
                        <th>Type</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($absences as $abs): ?>
                    <tr>
                        <td>
                            <div class="employee-cell">
                                <div class="employee-avatar">
                                    <?php if ($abs['photo']): ?>
                                        <img src="../<?= $abs['photo'] ?>" alt="Photo">
                                    <?php else: ?>
                                        <span><?= substr($abs['nom'], 0, 1) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600;"><?= $abs['nom'] . ' ' . $abs['prenom'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?= date('d/m', strtotime($abs['date_debut'])) ?> - <?= date('d/m/Y', strtotime($abs['date_fin'])) ?>
                            <br>
                            <small class="text-muted"><?= (strtotime($abs['date_fin']) - strtotime($abs['date_debut'])) / (60 * 60 * 24) + 1 ?> jours</small>
                        </td>
                        <td><span class="badge badge-warning"><?= $abs['type_absence'] ?></span></td>
                        <td style="color: var(--text-muted); font-style: italic;"><?= $abs['motif'] ?></td>
                        <td>
                            <?php if ($abs['justifie']): ?>
                                <span class="badge badge-success">Justifié</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Injustifié</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <form action="../controller/absence_controller.php" method="POST" onsubmit="return confirm('Supprimer cette absence ?');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_absence">
                                    <input type="hidden" name="id" value="<?= $abs['id'] ?>">
                                    <button type="submit" class="action-btn action-delete" title="Supprimer"><i class='bx bx-trash'></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Retard Modal -->
<div id="addRetardModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <span style="background: var(--red-primary); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class='bx bx-timer'></i>
                </span>
                Nouveau Retard
            </h3>
            <button class="modal-close" onclick="closeModal('addRetardModal')"><i class='bx bx-x'></i></button>
        </div>
        <form action="../controller/retard_controller.php" method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="add_retard">
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-user'></i> Employé</label>
                    <select name="id_employe" class="form-control" required>
                        <?php foreach ($employes as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= $emp['nom'] . ' ' . $emp['prenom'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-calendar'></i> Date</label>
                    <input type="date" name="date_retard" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-time'></i> Durée (minutes)</label>
                    <input type="number" name="duree_minutes" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-note'></i> Motif</label>
                    <textarea name="motif" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" style="cursor: pointer;">
                        <input type="checkbox" name="justifie" style="width: auto; margin-right: 10px;"> 
                        Retard Justifié ?
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addRetardModal')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Heure Sup Modal -->
<div id="addHeureSupModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <span style="background: var(--green-primary, #059669); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class='bx bx-up-arrow-circle'></i>
                </span>
                Nouvelle Heure Sup
            </h3>
            <button class="modal-close" onclick="closeModal('addHeureSupModal')"><i class='bx bx-x'></i></button>
        </div>
        <form action="../controller/heures_sup_controller.php" method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="add_heure_sup">
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-user'></i> Employé</label>
                    <select name="id_employe" class="form-control" required>
                        <?php foreach ($employes as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= $emp['nom'] . ' ' . $emp['prenom'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-calendar'></i> Date</label>
                    <input type="date" name="date_heure" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-time'></i> Durée (minutes)</label>
                    <input type="number" name="duree_minutes" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-note'></i> Motif / Tâche</label>
                    <textarea name="motif" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" style="cursor: pointer;">
                        <input type="checkbox" name="valide" checked style="width: auto; margin-right: 10px;"> 
                        Validé ?
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addHeureSupModal')">Annuler</button>
                <button type="submit" class="btn btn-success">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Absence Modal -->
<div id="addAbsenceModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <span style="background: var(--red-primary); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class='bx bx-calendar-x'></i>
                </span>
                Nouvelle Absence
            </h3>
            <button class="modal-close" onclick="closeModal('addAbsenceModal')"><i class='bx bx-x'></i></button>
        </div>
        <form action="../controller/absence_controller.php" method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="add_absence">
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-user'></i> Employé</label>
                    <select name="id_employe" class="form-control" required>
                        <?php foreach ($employes as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= $emp['nom'] . ' ' . $emp['prenom'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label class="form-label"><i class='bx bx-calendar'></i> Début</label>
                        <input type="date" name="date_debut" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label"><i class='bx bx-calendar'></i> Fin</label>
                        <input type="date" name="date_fin" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-list-ul'></i> Type</label>
                    <select name="type_absence" class="form-control">
                        <option value="Maladie">Maladie</option>
                        <option value="Injustifié" selected>Injustifié</option>
                        <option value="Autorisé">Autorisé</option>
                        <option value="Maternité">Maternité</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class='bx bx-note'></i> Motif</label>
                    <textarea name="motif" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" style="cursor: pointer;">
                        <input type="checkbox" name="justifie" style="width: auto; margin-right: 10px;"> 
                        Justifié ?
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addAbsenceModal')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
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

    function switchTab(tabId) {
        const tabs = document.getElementsByClassName('tab-content');
        for (let i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('active');
        }
        
        const btns = document.getElementsByClassName('tab-btn');
        for (let i = 0; i < btns.length; i++) {
            btns[i].classList.remove('active');
        }
        
        document.getElementById('tab-' + tabId).classList.add('active');
        // Find the button that called this specific ID and add active class based on index logic or direct element ref
        // For simplicity here, re-looping based on index might be tricky if not passed 'this'.
        // So we just rely on page reload or simple DOM lookup
        const activeBtn = document.querySelector(`.tab-btn[onclick="switchTab('${tabId}')"]`);
        if(activeBtn) activeBtn.classList.add('active');
    }
</script>

<?php include 'pied.php'; ?>
</body>
</html>
