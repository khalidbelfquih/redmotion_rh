<?php
include 'entete.php';
require_once '../model/hr_functions.php';
$employes = getEmployes();
// Fetch Event Types
$stmt = $connexion->query("SELECT * FROM event_types ORDER BY nom ASC");
$eventTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<style>
    /* FullCalendar Customization to match Red Motion */
    :root {
        --fc-border-color: var(--border-light);
        --fc-button-bg-color: white;
        --fc-button-border-color: var(--border-medium);
        --fc-button-text-color: var(--text-dark);
        --fc-button-hover-bg-color: var(--bg-light);
        --fc-button-active-bg-color: var(--red-primary);
        --fc-button-active-border-color: var(--red-primary);
        --fc-today-bg-color: rgba(230, 57, 70, 0.05);
        --fc-event-bg-color: var(--red-primary);
        --fc-event-border-color: var(--red-primary);
    }
    
    .home-content { padding: 24px; }

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

    /* Stats Cards */
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

    /* Filter Chips */
    .filter-section {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 24px;
        align-items: center;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: white;
        border: 1px solid var(--border-medium);
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-medium);
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-chip:hover {
        background: var(--bg-light);
    }

    .filter-chip.active {
        background: var(--text-dark);
        color: white;
        border-color: var(--text-dark);
    }

    .filter-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #ccc;
    }

    .filter-chip.active .filter-dot {
        background-color: var(--red-primary);
    }

    /* Calendar & Side Panel Layout */
    .main-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .main-layout { grid-template-columns: 1fr; }
    }

    .calendar-container {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--border-light);
    }

    .side-panel {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--border-light);
        height: fit-content;
    }

    .panel-header {
        border-bottom: 1px solid var(--border-light);
        padding-bottom: 16px;
        margin-bottom: 16px;
    }
    
    .panel-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .leave-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: var(--bg-light);
        border-radius: 10px;
        margin-bottom: 8px;
        transition: transform 0.2s;
    }
    
    .leave-item:hover { transform: translateX(4px); }

    .leave-avatar {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        color: var(--text-dark);
        box-shadow: var(--shadow-soft);
    }

    /* FullCalendar Overrides */
    .fc .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 700; color: var(--text-dark); }
    .fc-event { border: none !important; border-radius: 6px !important; padding: 2px 4px !important; font-size: 0.8rem !important; font-weight: 500; }
    
    .event-conge { background-color: #3B82F6 !important; }
    .event-presence { background-color: #10B981 !important; }
    .event-standard { background-color: var(--red-primary) !important; }

    /* Buttons */
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
    
    .btn-primary { background-color: var(--red-primary); }
    .btn-primary:hover { background-color: var(--red-dark); }
    
    .btn-secondary { background-color: white; color: var(--text-medium); border: 1px solid var(--border-light); }
    .btn-secondary:hover { background-color: var(--bg-light); }

    /* Modal Styling Fixes */
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
        max-width: 500px;
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
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-body { padding: 24px; }

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

    .form-group { margin-bottom: 16px; }
    
    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: var(--text-medium);
        font-size: 0.9rem;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-medium);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        background-color: white;
        color: var(--text-dark);
    }
    
    .form-control:focus {
        border-color: var(--red-primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
    }
</style>

<div class="home-content">
    <!-- Header -->
    <div class="header-section">
        <h2 class="page-title">Planning & Calendrier</h2>
        <div style="display:flex; gap:10px;">
            <button onclick="openAddModal()" class="btn btn-primary" data-tooltip="Créer un nouvel événement d'entreprise">
                <i class='bx bx-plus'></i> Nouvel Événement
            </button>
            <button onclick="openPresenceModal()" class="btn btn-secondary" data-tooltip="Planifier la présence d'un employé">
                <i class='bx bx-time'></i> Planifier
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-title">En Congé ce Mois</div>
            <div class="summary-value" id="stat_conges">-</div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Présences Planifiées</div>
            <div class="summary-value" id="stat_presences" style="color: #10B981;">-</div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Événements à Venir</div>
            <div class="summary-value" id="stat_events" style="color: var(--red-primary);">-</div>
        </div>
    </div>

    <!-- Controls Row -->
    <div class="filter-section">
        <div class="filter-chip active" onclick="toggleFilter(this, 'filter_conges')">
            <input type="checkbox" id="filter_conges" checked style="display:none;">
            <span class="filter-dot"></span>
            Congés
        </div>
        <div class="filter-chip active" onclick="toggleFilter(this, 'filter_presences')">
            <input type="checkbox" id="filter_presences" checked style="display:none;">
            <span class="filter-dot"></span>
            Présences
        </div>
    </div>

    <!-- Main Layout -->
    <div class="main-layout">
        <!-- Calendar -->
        <div class="calendar-container">
            <div id='calendar'></div>
        </div>

        <!-- Side Panel -->
        <div class="side-panel">
            <div class="panel-header">
                <div class="panel-title">En Congé Aujourd'hui</div>
                <div style="font-size: 0.85rem; color: var(--text-medium); margin-top: 4px;" id="today_date"></div>
            </div>
            <div class="leave-list" id="today_leaves">
                <div style="text-align: center; padding: 20px; color: var(--text-medium);">
                    <i class='bx bx-check-circle' style="font-size: 24px; color: #10B981;"></i>
                    <p style="margin-top: 8px; font-size: 13px;">Tout le monde est présent !</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <span style="background: var(--red-primary); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class='bx bx-calendar-event'></i>
                </span>
                Nouvel Événement
            </h3>
            <button onclick="closeModal('eventModal')" style="background:none; border:none; font-size:20px; cursor:pointer; color: var(--text-medium);"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form id="eventForm">
                <div class="form-group">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" id="evt_titre" class="form-control" placeholder="Ex: Réunion d'équipe" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" id="evt_type" class="form-control">
                        <?php if(empty($eventTypes)): ?>
                            <option value="reunion">🤝 Réunion (Défaut)</option>
                        <?php else: ?>
                            <?php foreach ($eventTypes as $type): ?>
                                <option value="<?= strtolower($type['nom']) ?>">
                                    <?= htmlspecialchars($type['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Début</label>
                        <input type="datetime-local" name="date_debut" id="evt_start" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fin</label>
                        <input type="datetime-local" name="date_fin" id="evt_end" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="evt_desc" class="form-control" rows="3" placeholder="Détails de l'événement..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('eventModal')" class="btn btn-secondary">Annuler</button>
            <button onclick="saveEvent()" class="btn btn-primary">Enregistrer</button>
        </div>
    </div>
</div>

<!-- Add Presence Modal -->
<div id="presenceModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                 <span style="background: #10B981; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class='bx bx-time'></i>
                </span>
                Planifier Présence
            </h3>
            <button onclick="closeModal('presenceModal')" style="background:none; border:none; font-size:20px; cursor:pointer; color: var(--text-medium);"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form id="presenceForm">
                <div class="form-group">
                    <label class="form-label">Employé</label>
                    <select name="id_employe" class="form-control" required>
                        <?php foreach ($employes as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['nom'] . ' ' . $emp['prenom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="date_planning" id="pres_date" class="form-control" required>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Heure Début</label>
                        <input type="time" name="heure_debut" class="form-control" value="09:00" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Heure Fin</label>
                        <input type="time" name="heure_fin" class="form-control" value="18:00" required>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('presenceModal')" class="btn btn-secondary">Annuler</button>
            <button onclick="savePresence()" class="btn btn-primary" style="background-color: #10B981;">Enregistrer</button>
        </div>
    </div>
</div>

<!-- View Event Modal -->
<div id="viewEventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="view_evt_title"></h3>
            <button onclick="closeModal('viewEventModal')" style="background:none; border:none; font-size:20px; cursor:pointer; color: var(--text-medium);"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <p id="view_evt_time" style="font-weight: 600; color: var(--text-dark); margin-bottom: 12px; font-size: 1rem;"></p>
            <p id="view_evt_desc" style="color: var(--text-medium); line-height: 1.5;"></p>
            <input type="hidden" id="view_evt_id">
            <input type="hidden" id="view_evt_type">
        </div>
        <div class="modal-footer">
            <button onclick="deleteEvent()" class="btn" style="background-color: #EF4444; color: white;" id="btn_delete_evt" data-tooltip="Supprimer cet événement définitivement">
                <i class='bx bx-trash'></i> Supprimer
            </button>
            <button onclick="closeModal('viewEventModal')" class="btn btn-secondary">Fermer</button>
        </div>
    </div>
</div>

<script>
    let calendar;

    // Set today's date
    document.getElementById('today_date').textContent = new Date().toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long'
    });

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'fr',
            buttonText: {
                today: "Aujourd'hui",
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour'
            },
            height: 'auto',
            events: function(info, successCallback, failureCallback) {
                const showConges = document.getElementById('filter_conges').checked ? 1 : 0;
                const showPresences = document.getElementById('filter_presences').checked ? 1 : 0;
                
                fetch(`../controller/planning_controller.php?action=get_events&start=${info.startStr}&end=${info.endStr}&show_leaves=${showConges}&show_presences=${showPresences}`)
                .then(response => response.json())
                .then(data => {
                    const events = data.map(event => {
                        let className = 'event-standard';
                        if(event.extendedProps && event.extendedProps.type === 'conge') className = 'event-conge';
                        if(event.extendedProps && event.extendedProps.type === 'presence') className = 'event-presence';
                        
                        return { ...event, classNames: [className] };
                    });
                    
                    // Update stats
                    updateStats(events);
                    
                    successCallback(events);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
            },
            eventClick: function(info) {
                const props = info.event.extendedProps;
                
                // Don't show modal for leaves, they are read-only here
                if(props.type === 'conge') return;

                document.getElementById('view_evt_title').textContent = info.event.title;
                document.getElementById('view_evt_id').value = info.event.id;
                document.getElementById('view_evt_type').value = props.type;
                
                let timeStr = info.event.start.toLocaleDateString('fr-FR') + ' ' + 
                              info.event.start.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});
                
                if(info.event.end) {
                    timeStr += ' - ' + info.event.end.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});
                }
                
                document.getElementById('view_evt_time').textContent = timeStr;
                document.getElementById('view_evt_desc').textContent = props.description || 'Aucune description';
                
                // Hide delete for presences (logic can be flexible)
                // document.getElementById('btn_delete_evt').style.display = props.type === 'presence' ? 'none' : 'block';
                
                openModal('viewEventModal');
            }
        });
        calendar.render();
        loadTodayLeaves();
    });

    function updateStats(events) {
        let congesCount = 0;
        let presencesCount = 0;
        let eventsCount = 0;

        events.forEach(e => {
            if(e.extendedProps.type === 'conge') congesCount++;
            else if(e.extendedProps.type === 'presence') presencesCount++;
            else eventsCount++;
        });

        document.getElementById('stat_conges').textContent = congesCount;
        document.getElementById('stat_presences').textContent = presencesCount;
        document.getElementById('stat_events').textContent = eventsCount;
    }

    function toggleFilter(element, checkboxId) {
        const checkbox = document.getElementById(checkboxId);
        checkbox.checked = !checkbox.checked;
        
        if (checkbox.checked) {
            element.classList.add('active');
        } else {
            element.classList.remove('active');
        }
        
        calendar.refetchEvents();
    }

    /* Modal Functions */
    function openAddModal() {
        document.getElementById('eventForm').reset();
        openModal('eventModal');
    }

    function openPresenceModal() {
        document.getElementById('presenceForm').reset();
        document.getElementById('pres_date').valueAsDate = new Date();
        openModal('presenceModal');
    }

    function openModal(id) {
        document.getElementById(id).style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }

    /* AJAX Operations */
    function saveEvent() {
        const formData = new FormData(document.getElementById('eventForm'));
        formData.append('action', 'add_event');

        fetch('../controller/planning_controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                closeModal('eventModal');
                calendar.refetchEvents();
                // Toast logic could go here
            } else {
                alert('Erreur: ' + (data.message || 'Une erreur inconnue est survenue'));
            }
        });
    }

    function savePresence() {
        const formData = new FormData(document.getElementById('presenceForm'));
        formData.append('action', 'add_presence');

        fetch('../controller/planning_controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                closeModal('presenceModal');
                calendar.refetchEvents();
            } else {
                alert('Erreur: ' + data.message);
            }
        });
    }

    function deleteEvent() {
        const id = document.getElementById('view_evt_id').value;
        const type = document.getElementById('view_evt_type').value;

        if(!confirm('Voulez-vous vraiment supprimer cet élément ?')) return;

        fetch('../controller/planning_controller.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=delete_event&id=${id}&type=${type}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                closeModal('viewEventModal');
                calendar.refetchEvents();
            } else {
                alert('Erreur: ' + data.message);
            }
        });
    }

    function loadTodayLeaves() {
        fetch('../controller/planning_controller.php?action=get_today_leaves')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('today_leaves');
            container.innerHTML = '';

            if(data.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 20px; color: var(--text-medium);">
                        <i class='bx bx-check-circle' style="font-size: 24px; color: #10B981;"></i>
                        <p style="margin-top: 8px; font-size: 13px;">Tout le monde est présent !</p>
                    </div>`;
                return;
            }

            data.forEach(leaf => {
                const item = `
                    <div class="leave-item">
                        <div class="leave-avatar">
                            ${leaf.initials}
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 14px; color: var(--text-dark);">${leaf.nom}</div>
                            <div style="font-size: 12px; color: var(--text-medium);">Jusqu'au ${new Date(leaf.date_fin).toLocaleDateString()}</div>
                        </div>
                    </div>
                `;
                container.innerHTML += item;
            });
        });
    }
</script>

<?php include 'pied.php'; ?>