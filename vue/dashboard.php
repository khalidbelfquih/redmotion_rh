<?php
include 'entete.php';
require_once '../model/dashboard_functions.php';

$hrStats = getHRStats();
$leaveRequests = getLeaveRequests();
$payrollEvolution = getPayrollEvolution();
    // ... Existing fetches ...
    $departmentStats = getDepartmentStats();
    $birthdays = getUpcomingBirthdays();
    $anniversaries = getWorkAnniversaries();
    $recruitmentStats = getRecruitmentStats();
    
    // NEW Data
    $recruitmentFunnel = getRecruitmentFunnel();
    $seniorityStats = getSeniorityStats();
    
    // Prepare data for Chart.js
    $payrollMonths = [];
    $payrollTotals = [];
    foreach ($payrollEvolution as $data) {
        if (isset($data['annee']) && isset($data['mois'])) {
            $payrollMonths[] = date('M', strtotime($data['annee'] . '-' . $data['mois'] . '-01'));
            $payrollTotals[] = $data['total'];
        }
    }

    $deptLabels = [];
    $deptCounts = [];
    foreach ($departmentStats as $dept) {
        $deptLabels[] = $dept['nom'];
        $deptCounts[] = $dept['count'];
    }
    
    // Seniority Data
    $seniorityLabels = array_keys($seniorityStats);
    $seniorityData = array_values($seniorityStats);
    
    // Funnel Data
    $funnelLabels = array_keys($recruitmentFunnel);
    $funnelData = array_values($recruitmentFunnel);
?>

<div class="home-content dashboard-container">
    <div class="header-section">
        <div>
            <h2 class="page-title">Tableau de Bord RH</h2>
            <p class="subtitle">Vue d'ensemble et statistiques en temps réel</p>
        </div>
        <div class="date-widget">
            <div class="date-icon">
                <i class='bx bx-calendar'></i>
            </div>
            <div>
                <span class="date-day"><?= date('l') ?></span>
                <span class="date-full"><?= date('d F Y') ?></span>
            </div>
        </div>
    </div>

    <!-- 1. KPI Cards Row -->
    <div class="kpi-grid">
        <!-- Card 1: Employés Actifs -->
        <div class="kpi-card primary-card">
            <div class="kpi-content">
                <span class="kpi-label">Employés Actifs</span>
                <h3 class="kpi-value"><?= $hrStats['total_active'] ?></h3>
                <div class="kpi-trend">
                    <i class='bx bx-trending-up'></i>
                    <span>Effectif total</span>
                </div>
            </div>
            <div class="kpi-icon-overlay">
                <i class='bx bx-group'></i>
            </div>
        </div>

        <!-- Card 2: Présents -->
        <div class="kpi-card secondary-card">
            <div class="kpi-content">
                <span class="kpi-label">Présents Aujourd'hui</span>
                <h3 class="kpi-value"><?= $hrStats['present_today'] ?></h3>
                <div class="kpi-trend">
                    <i class='bx bx-check-circle'></i>
                    <span>Sur site</span>
                </div>
            </div>
            <div class="kpi-icon-overlay">
                <i class='bx bx-user-check'></i>
            </div>
        </div>

        <!-- Card 3: En Congé -->
        <div class="kpi-card light-card">
            <div class="kpi-content">
                <span class="kpi-label">En Congé</span>
                <h3 class="kpi-value"><?= $hrStats['on_leave'] ?></h3>
                <div class="kpi-trend text-orange">
                    <i class='bx bx-sun'></i>
                    <span>Absentéisme légal</span>
                </div>
            </div>
            <div class="kpi-icon-overlay text-orange">
                <i class='bx bx-calendar-minus'></i>
            </div>
        </div>

        <!-- Card 4: Retards -->
        <div class="kpi-card white-card">
            <div class="kpi-content">
                <span class="kpi-label">Retards</span>
                <h3 class="kpi-value"><?= $hrStats['late_today'] ?></h3>
                <div class="kpi-trend text-red">
                    <i class='bx bx-time'></i>
                    <span>À surveiller</span>
                </div>
            </div>
            <div class="kpi-icon-overlay text-red">
                <i class='bx bx-timer'></i>
            </div>
        </div>
    </div>

    <!-- 2. Main Charts & Stats Grid -->
    <div class="dashboard-grid">
        
        <!-- Left: Charts -->
        <div class="charts-column">
            <!-- Row 1: Dept & Payroll -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <!-- Department Distribution -->
                <div class="widget-card">
                    <div class="widget-header">
                        <h3><i class='bx bx-pie-chart-alt-2'></i> Par Département</h3>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>

                <!-- Seniority Distribution -->
                <div class="widget-card">
                    <div class="widget-header">
                        <h3><i class='bx bx-time-five'></i> Ancienneté</h3>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="seniorityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Payroll Evolution -->
            <div class="widget-card">
                <div class="widget-header">
                    <h3><i class='bx bx-line-chart'></i> Masse Salariale</h3>
                </div>
                <div class="chart-wrapper" style="height: 200px;">
                    <canvas id="payrollChart"></canvas>
                </div>
            </div>
            
            <!-- Recruitment Funnel -->
             <div class="widget-card">
                <div class="widget-header">
                    <h3><i class='bx bx-filter-alt'></i> Entonnoir Recrutement</h3>
                </div>
                <div class="chart-wrapper" style="height: 200px;">
                    <canvas id="funnelChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right: Activities & Lists -->
        <div class="lists-column">
            
            <!-- Pending Leaves -->
            <div class="widget-card">
                <div class="widget-header">
                    <h3><i class='bx bx-bell'></i> Demandes de Congé</h3>
                    <a href="conges.php" class="widget-link" data-tooltip="Consulter toutes les demandes de congé">Voir tout</a>
                </div>
                <div class="widget-body">
                    <?php if (empty($leaveRequests)): ?>
                        <div class="empty-placeholder">
                            <i class='bx bx-check-shield'></i>
                            <p>Aucune demande en attente</p>
                        </div>
                    <?php else: ?>
                        <div class="list-items">
                            <?php foreach ($leaveRequests as $req): ?>
                            <div class="list-item">
                                <div class="item-avatar">
                                    <?= strtoupper(substr($req['prenom'], 0, 1) . substr($req['nom'], 0, 1)) ?>
                                </div>
                                <div class="item-info">
                                    <h4><?= htmlspecialchars($req['prenom'] . ' ' . $req['nom']) ?></h4>
                                    <p><?= htmlspecialchars($req['type_conge']) ?> • <?= date('d/m', strtotime($req['date_debut'])) ?></p>
                                </div>
                                <div class="item-action">
                                    <a href="conges.php" class="btn-icon" data-tooltip="Voir les détails de la demande"><i class='bx bx-chevron-right'></i></a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Events (Birthdays & Anniversaries) -->
            <div class="widget-card">
                <div class="widget-header">
                    <h3><i class='bx bx-cake'></i> Événements à venir</h3>
                </div>
                <div class="widget-body">
                    <div class="events-tabs">
                        <button class="event-tab active" onclick="switchEventTab('birthdays')" data-tooltip="Voir les prochains anniversaires">Anniversaires</button>
                        <button class="event-tab" onclick="switchEventTab('work')" data-tooltip="Voir les prochaines anciennetés">Ancienneté</button>
                    </div>

                    <div id="birthdays-list" class="event-list-container active">
                        <?php if (empty($birthdays)): ?>
                            <div class="empty-placeholder-sm">Rien à signaler ce mois-ci</div>
                        <?php else: ?>
                            <?php foreach ($birthdays as $bd): ?>
                            <div class="event-item">
                                <div class="event-date">
                                    <span class="day"><?= date('d', strtotime($bd['date_naissance'])) ?></span>
                                    <span class="month"><?= date('M', strtotime($bd['date_naissance'])) ?></span>
                                </div>
                                <div class="event-details">
                                    <h4><?= htmlspecialchars($bd['prenom'] . ' ' . $bd['nom']) ?></h4>
                                    <p>Dans <?= $bd['days_remaining'] ?> jours</p>
                                </div>
                                <i class='bx bx-cake event-icon'></i>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div id="work-list" class="event-list-container">
                        <?php if (empty($anniversaries)): ?>
                            <div class="empty-placeholder-sm">Aucun anniversaire pro bientôt</div>
                        <?php else: ?>
                            <?php foreach ($anniversaries as $an): ?>
                            <div class="event-item">
                                <div class="event-date blue">
                                    <span class="day"><?= date('d', strtotime($an['date_embauche'])) ?></span>
                                    <span class="month"><?= date('M', strtotime($an['date_embauche'])) ?></span>
                                </div>
                                <div class="event-details">
                                    <h4><?= htmlspecialchars($an['prenom'] . ' ' . $an['nom']) ?></h4>
                                    <p><?= $an['years'] ?> ans d'ancienneté</p>
                                </div>
                                <i class='bx bx-medal event-icon'></i>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
    :root {
        --dash-bg: #f8f9fa;
        --card-bg: #ffffff;
        --text-primary: #2d3748;
        --text-secondary: #718096;
        --red-primary: #E63946;
        --red-dark: #B71C1C;
        --red-light: #FFE5E5;
    }

    .dashboard-container {
        padding: 24px;
        background-color: var(--dash-bg);
        min-height: 100vh;
    }

    /* Header */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 32px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 4px 0;
    }

    .subtitle {
        color: var(--text-secondary);
        font-size: 14px;
        margin: 0;
    }

    .date-widget {
        background: white;
        padding: 8px 16px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }

    .date-icon {
        width: 36px;
        height: 36px;
        background: var(--red-light);
        color: var(--red-primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .date-day {
        display: block;
        font-size: 11px;
        color: var(--text-secondary);
        text-transform: uppercase;
        font-weight: 600;
    }

    .date-full {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* KPI Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .kpi-card {
        border-radius: 16px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .kpi-card:hover {
        transform: translateY(-4px);
    }

    .primary-card {
        background: linear-gradient(135deg, var(--red-primary), 80%, black);
        color: white;
    }

    .secondary-card {
        background: linear-gradient(135deg, #1f2937, #111827);
        color: white;
    }

    .light-card {
        background: #fff;
        border: 1px solid #eee;
    }

    .white-card {
        background: #fff;
        border: 1px solid #eee;
    }

    .kpi-content {
        position: relative;
        z-index: 2;
    }

    .kpi-label {
        font-size: 13px;
        font-weight: 500;
        opacity: 0.9;
        display: block;
        margin-bottom: 8px;
    }

    .kpi-value {
        font-size: 32px;
        font-weight: 800;
        margin: 0 0 8px 0;
    }

    .kpi-trend {
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
        opacity: 0.8;
    }

    .kpi-icon-overlay {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 80px;
        opacity: 0.1;
        z-index: 1;
    }

    .text-orange { color: #ed8936; }
    .text-red { color: var(--red-primary); }

    /* Dashboard Grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    
    @media (max-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    .charts-column, .lists-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .widget-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border: 1px solid #f0f0f0;
    }

    .widget-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .widget-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .widget-header h3 i {
        color: var(--red-primary);
        font-size: 18px;
    }

    .widget-link {
        font-size: 12px;
        color: var(--text-secondary);
        text-decoration: none;
        font-weight: 600;
    }
    
    .chart-wrapper {
        height: 250px;
        position: relative;
    }

    /* Lists */
    .list-items {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .list-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f7f7f7;
    }
    
    .list-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .item-avatar {
        width: 40px;
        height: 40px;
        background: var(--red-light);
        color: var(--red-primary);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
    }

    .item-info h4 {
        margin: 0 0 2px 0;
        font-size: 14px;
        color: var(--text-primary);
    }

    .item-info p {
        margin: 0;
        font-size: 12px;
        color: var(--text-secondary);
    }

    .item-action {
        margin-left: auto;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f7f7f7;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-secondary);
        transition: all 0.2s;
    }
    
    .btn-icon:hover {
        background: var(--red-primary);
        color: white;
    }

    .empty-placeholder {
        text-align: center;
        padding: 30px;
        color: #cbd5e0;
    }
    
    .empty-placeholder i {
        font-size: 32px;
        display: block;
        margin-bottom: 8px;
    }

    /* Events Tab */
    .events-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .event-tab {
        background: none;
        border: none;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        border-bottom: 2px solid transparent;
    }
    
    .event-tab.active {
        color: var(--red-primary);
        border-bottom-color: var(--red-primary);
    }

    .event-list-container {
        display: none;
        flex-direction: column;
        gap: 16px;
    }
    
    .event-list-container.active {
        display: flex;
    }

    .event-item {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fdfdfd;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #f0f0f0;
    }

    .event-date {
        background: var(--red-light);
        color: var(--red-primary);
        padding: 6px 10px;
        border-radius: 8px;
        text-align: center;
        min-width: 50px;
    }
    
    .event-date.blue {
        background: #ebf8ff;
        color: #4299e1;
    }

    .event-date .day {
        display: block;
        font-size: 16px;
        font-weight: 700;
        line-height: 1;
    }
    
    .event-date .month {
        font-size: 10px;
        text-transform: uppercase;
    }

    .event-details h4 {
        margin: 0;
        font-size: 14px;
        color: var(--text-primary);
    }
    
    .event-details p {
        margin: 2px 0 0 0;
        font-size: 12px;
        color: var(--text-secondary);
    }
    
    .event-icon {
        margin-left: auto;
        color: #cbd5e0;
        font-size: 20px;
    }
    
    .empty-placeholder-sm {
        text-align: center;
        color: var(--text-secondary);
        font-size: 12px;
        padding: 10px;
    }
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Tab Switching
    function switchEventTab(tabName) {
        document.querySelectorAll('.event-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.event-list-container').forEach(c => c.classList.remove('active'));
        
        event.target.classList.add('active');
        
        if(tabName === 'birthdays') {
            document.getElementById('birthdays-list').classList.add('active');
        } else {
            document.getElementById('work-list').classList.add('active');
        }
    }

    // --- Charts ---
    
    // 1. Department Chart
    const ctxDept = document.getElementById('deptChart').getContext('2d');
    new Chart(ctxDept, {
        type: 'bar',
        data: {
            labels: <?= json_encode($deptLabels) ?>,
            datasets: [{
                label: 'Effectif',
                data: <?= json_encode($deptCounts) ?>,
                backgroundColor: '#E63946',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Payroll Chart (Red Theme)
    const ctxPayroll = document.getElementById('payrollChart').getContext('2d');
    new Chart(ctxPayroll, {
        type: 'line',
        data: {
            labels: <?= json_encode($payrollMonths) ?>,
            datasets: [{
                label: 'Masse Salariale',
                data: <?= json_encode($payrollTotals) ?>,
                borderColor: '#1f2937',
                backgroundColor: 'rgba(31, 41, 55, 0.05)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#1f2937',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                x: { grid: { display: false } }
            }
        }
    });

    // 3. Seniority Chart
    const ctxSeniority = document.getElementById('seniorityChart').getContext('2d');
    new Chart(ctxSeniority, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($seniorityLabels) ?>,
            datasets: [{
                data: <?= json_encode($seniorityData) ?>,
                backgroundColor: [
                    '#E63946', // Red
                    '#457B9D', // Blue
                    '#A8DADC', // Light Blue
                    '#1D3557'  // Dark Blue
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } }
            },
            cutout: '60%'
        }
    });
    
    // 4. Recruitment Funnel Chart
    const ctxFunnel = document.getElementById('funnelChart').getContext('2d');
    new Chart(ctxFunnel, {
        type: 'bar',
        data: {
            labels: <?= json_encode($funnelLabels) ?>,
            datasets: [{
                label: 'Candidats',
                data: <?= json_encode($funnelData) ?>,
                backgroundColor: [
                    '#A8DADC', // Nouveau
                    '#457B9D', // Entretien
                    '#1D3557', // Embauché
                    '#E63946'  // Rejeté
                ],
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y', // Horizontal Bar Chart
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true, grid: { display: false } },
                y: { grid: { display: false } }
            }
        }
    });

    // --- Counter Animation ---
    const kpiValues = document.querySelectorAll('.kpi-value');
    
    kpiValues.forEach(counter => {
        const target = +counter.innerText;
        
        // If target is 0, just show 0
        if(target === 0) return;
        
        // Determine animation duration based on magnitude of number
        // Max duration 2 seconds (2000ms), min 0.5s
        const duration = 1500; 
        const frameRate = 15; // ms per frame
        const totalFrames = duration / frameRate;
        const increment = target / totalFrames;
        
        counter.innerText = '0';
        
        let currentCount = 0;
        
        const updateCount = () => {
            currentCount += increment;
            
            if (currentCount < target) {
                counter.innerText = Math.ceil(currentCount);
                setTimeout(updateCount, frameRate);
            } else {
                counter.innerText = target;
            }
        };
        
        updateCount();
    });
</script>

<?php include 'pied.php'; ?>
