<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Articles Excel</title>
    <style>
        /* Styles généraux responsive */
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
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            margin: 0;
            padding: 20px;
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
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn:active {
            transform: scale(0.98);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        .upload-zone {
            border: 3px dashed var(--border-color);
            border-radius: var(--border-radius);
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--light-bg);
            margin: 20px 0;
        }
        
        .upload-zone:hover, .upload-zone.dragover {
            border-color: var(--secondary-color);
            background: #e6f7f5;
            transform: translateY(-2px);
        }
        
        .upload-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            background: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
        }
        
        .upload-text {
            font-size: 18px;
            color: var(--text-color);
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .upload-subtext {
            color: var(--text-muted);
            font-size: 14px;
        }
        
        .file-input {
            display: none;
        }
        
        .selected-file {
            background: #e6fffa;
            border: 1px solid #81e6d9;
            border-radius: var(--border-radius);
            padding: 15px;
            margin: 20px 0;
            display: none;
        }
        
        .selected-file.show {
            display: block;
        }
        
        .file-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .file-icon {
            width: 40px;
            height: 40px;
            background: var(--secondary-color);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .progress-container {
            margin: 20px 0;
            display: none;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .progress-text {
            text-align: center;
            margin-top: 10px;
            color: var(--text-muted);
            font-size: 14px;
        }
        
        .data-table-wrapper {
            overflow-x: auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin: 20px 0;
            display: none;
        }
        
        .data-table-wrapper.show {
            display: block;
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
            position: sticky;
            top: 0;
        }
        
        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        
        .data-table tbody tr {
            transition: background-color 0.2s;
        }
        
        .data-table tbody tr:hover {
            background-color: rgba(42, 157, 143, 0.05);
        }
        
        .data-table tbody tr.error {
            background-color: rgba(231, 111, 81, 0.1);
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
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left-color: #ffc107;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left-color: #17a2b8;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
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
        
        .summary-card.accent .summary-value {
            color: var(--accent-color);
        }
        
        .summary-card.success .summary-value {
            color: var(--secondary-color);
        }
        
        .instructions {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
        }
        
        .instructions h3 {
            margin-top: 0;
            margin-bottom: 15px;
        }
        
        .instructions ol {
            margin-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 8px;
        }
        
        /* Responsive adaptations */
        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .btn-actions {
                width: 100%;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .summary-cards {
                flex-direction: column;
            }
            
            .summary-card {
                min-width: 100%;
            }
        }
    </style>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="home-content">
    <!-- En-tête de page -->
    <div class="header-section">
        <h2 class="page-title">Import Articles Excel</h2>
        <div class="btn-actions">
              <a href="vue/modele_articles_import.xlsx" download="modele_articles_import.xlsx" class="btn btn-primary">
    <i class='bx bx-download'></i> Télécharger Modèle
</a>
            <a href="https://visionka-homi.com/visionka/vue/article.php" class="btn btn-secondary">
                <i class='bx bx-arrow-back'></i> Retour
            </a>
        </div>
    </div>

    <!-- Instructions -->
    <div class="instructions">
        <h3><i class='bx bx-info-circle'></i> Instructions d'utilisation</h3>
        <ol>
            <li>Cliquez sur "Télécharger Modèle" pour récupérer le fichier CSV</li>
            <li>Remplissez le fichier avec vos données d'articles</li>
            <li>Uploadez le fichier rempli ci-dessous</li>
            <li>Vérifiez l'aperçu des données</li>
            <li>Confirmez l'insertion en base de données</li>
        </ol>
    </div>

    <!-- Zone d'upload -->
    <div class="card">
        <h3>Sélectionner le fichier Excel</h3>
        <div class="upload-zone" onclick="document.getElementById('fileInput').click()">
            <div class="upload-icon">
                <i class='bx bx-cloud-upload'></i>
            </div>
            <div class="upload-text">Cliquez pour sélectionner ou glissez-déposez</div>
            <div class="upload-subtext">Formats acceptés: .xlsx, .csv (Max: 10MB)</div>
        </div>
        
        <input type="file" id="fileInput" class="file-input" accept=".xlsx,.csv" onchange="handleFileSelect(event)">
        
        <!-- Fichier sélectionné -->
        <div id="selectedFile" class="selected-file">
            <div class="file-info">
                <div class="file-icon">
                    <i class='bx bx-file-blank'></i>
                </div>
                <div>
                    <div style="font-weight: 600;" id="fileName"></div>
                    <div style="color: #666; font-size: 14px;" id="fileSize"></div>
                </div>
            </div>
        </div>
        
        <!-- Barre de progression -->
        <div id="progressContainer" class="progress-container">
            <div class="progress-bar">
                <div id="progressFill" class="progress-fill"></div>
            </div>
            <div id="progressText" class="progress-text">Traitement en cours...</div>
        </div>
        
        <!-- Boutons d'action -->
        <div class="btn-actions" style="margin-top: 20px;">
            <button id="processBtn" onclick="processFile()" class="btn btn-success" disabled>
                <i class='bx bx-cog'></i> Traiter le fichier
            </button>
            <button onclick="resetForm()" class="btn btn-secondary">
                <i class='bx bx-reset'></i> Réinitialiser
            </button>
        </div>
    </div>

    <!-- Messages d'alerte -->
    <div id="alertContainer"></div>

    <!-- Résumé -->
    <div id="summaryCards" class="summary-cards" style="display: none;">
        <div class="summary-card">
            <div class="summary-title">Total lignes</div>
            <div class="summary-value" id="totalRows">0</div>
        </div>
        
        <div class="summary-card success">
            <div class="summary-title">Lignes valides</div>
            <div class="summary-value" id="validRows">0</div>
        </div>
        
        <div class="summary-card accent">
            <div class="summary-title">Erreurs</div>
            <div class="summary-value" id="errorRows">0</div>
        </div>
    </div>

    <!-- Aperçu des données -->
    <div id="previewContainer" class="data-table-wrapper">
        <h3>Aperçu des données</h3>
        <table class="data-table" id="previewTable">
            <thead>
                <tr>
                    <th>Statut</th>
                    <th>Nom Article</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>Référence</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Catégorie</th>
                    <th>Erreurs</th>
                </tr>
            </thead>
            <tbody id="previewTableBody">
            </tbody>
        </table>
        
        <div class="btn-actions" style="margin-top: 20px;">
            <button id="confirmBtn" onclick="confirmInsertion()" class="btn btn-success" disabled>
                <i class='bx bx-check-circle'></i> Confirmer l'insertion
            </button>
        </div>
    </div>
</div>

<script>
let processedData = [];

// Télécharger le modèle CSV
function downloadTemplate() {
    const csvContent = `nom_article,marque,modele,reference,couleur,matiere,forme,diametre,emplacement,id_categorie,quantite,prix_unitaire,date_fabrication,date_expiration,societe
ELEGANZE,ELEGANZE,"EL1042 C2 48_20_143",EL001,C2,Acetate,Rectangulaire,,exposé,1,5,1350,"2025-05-13 07:44:00","2026-05-13 07:44:00",Optique Vision`;
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'modele_articles.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

// Gestion du drag & drop
document.addEventListener('DOMContentLoaded', function() {
    const uploadZone = document.querySelector('.upload-zone');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight(e) {
        uploadZone.classList.add('dragover');
    }
    
    function unhighlight(e) {
        uploadZone.classList.remove('dragover');
    }
    
    uploadZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            handleFileSelect({ target: { files: files } });
        }
    }
});

// Sélection de fichier
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Vérification du type de fichier
    const allowedTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    ];
    
    if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx|csv)$/i)) {
        showAlert('Veuillez sélectionner un fichier Excel (.xlsx) ou CSV', 'danger');
        return;
    }
    
    // Vérification de la taille
    if (file.size > 10 * 1024 * 1024) {
        showAlert('Le fichier est trop volumineux (max 10MB)', 'danger');
        return;
    }
    
    // Afficher le fichier sélectionné
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatFileSize(file.size);
    document.getElementById('selectedFile').classList.add('show');
    document.getElementById('processBtn').disabled = false;
    
    // Masquer les résultats précédents
    document.getElementById('previewContainer').classList.remove('show');
    document.getElementById('summaryCards').style.display = 'none';
    clearAlerts();
}

// Traitement du fichier
async function processFile() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    
    if (!file) {
        showAlert('Aucun fichier sélectionné', 'danger');
        return;
    }
    
    // Afficher la progression
    document.getElementById('progressContainer').style.display = 'block';
    document.getElementById('processBtn').disabled = true;
    updateProgress(10);
    
    // Préparer FormData pour PHP
    const formData = new FormData();
    formData.append('excel_file', file);
    
    try {
        updateProgress(30);
        
        // Envoyer le fichier au serveur PHP
        const response = await fetch('traiter_excel_articles_final.php', {
            method: 'POST',
            body: formData
        });
        
        updateProgress(70);
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        
        updateProgress(90);
        
        if (result.error) {
            throw new Error(result.error);
        }
        
        if (result.success) {
            processedData = result.data;
            displayPreview();
            updateProgress(100);
            
            // Afficher message de succès avec statistiques
            showAlert(`Fichier traité avec succès ! ${result.stats.valid} lignes valides sur ${result.stats.total}`, 'success');
            
            if (result.stats.errors > 0) {
                showAlert(`${result.stats.errors} lignes contiennent des erreurs`, 'warning');
            }
        } else {
            throw new Error('Réponse inattendue du serveur');
        }
        
    } catch (error) {
        console.error('Erreur lors du traitement:', error);
        showAlert('Erreur lors du traitement du fichier: ' + error.message, 'danger');
        updateProgress(0);
        document.getElementById('progressContainer').style.display = 'none';
    } finally {
        document.getElementById('processBtn').disabled = false;
    }
}

// Afficher l'aperçu
function displayPreview() {
    const tbody = document.getElementById('previewTableBody');
    tbody.innerHTML = '';
    
    let totalRows = processedData.length;
    let validRows = 0;
    let errorRows = 0;
    
    processedData.forEach(row => {
        if (row.valid) {
            validRows++;
        } else {
            errorRows++;
        }
        
        const tr = document.createElement('tr');
        if (!row.valid) {
            tr.classList.add('error');
        }
        
        tr.innerHTML = `
            <td>
                <span class="badge ${row.valid ? 'badge-success' : 'badge-danger'}">
                    ${row.valid ? 'Valide' : 'Erreur'}
                </span>
            </td>
            <td>${row.nom_article || '-'}</td>
            <td>${row.marque || '-'}</td>
            <td>${row.modele || '-'}</td>
            <td>${row.reference || '-'}</td>
            <td>${row.prix_unitaire || '0'} DH</td>
            <td>${row.quantite || '0'}</td>
            <td>${row.id_categorie || '-'}</td>
            <td>${row.errors && row.errors.length > 0 ? row.errors.join(', ') : '-'}</td>
        `;
        
        tbody.appendChild(tr);
    });
    
    // Mettre à jour les résumés
    document.getElementById('totalRows').textContent = totalRows;
    document.getElementById('validRows').textContent = validRows;
    document.getElementById('errorRows').textContent = errorRows;
    
    // Afficher les résultats
    document.getElementById('summaryCards').style.display = 'flex';
    document.getElementById('previewContainer').classList.add('show');
    document.getElementById('confirmBtn').disabled = validRows === 0;
    
    // Masquer la progression
    document.getElementById('progressContainer').style.display = 'none';
}

// Confirmer l'insertion
async function confirmInsertion() {
    if (!confirm('Êtes-vous sûr de vouloir insérer ces articles en base de données ?')) {
        return;
    }
    
    const validData = processedData.filter(row => row.valid);
    
    if (validData.length === 0) {
        showAlert('Aucune donnée valide à insérer', 'warning');
        return;
    }
    
    document.getElementById('confirmBtn').disabled = true;
    showAlert('Insertion en cours...', 'info');
    
    try {
        const response = await fetch('insert_articles_clean.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                articles: validData
            })
        });
        
        // DÉBOGAGE : Voir la réponse brute
        const text = await response.text();
        console.log('Réponse brute du serveur:', text);
        console.log('Status:', response.status);
        console.log('Headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        // Essayer de parser le JSON
        let result;
        try {
            result = JSON.parse(text);
        } catch (jsonError) {
            console.error('Erreur JSON:', jsonError);
            console.error('Texte reçu:', text);
            throw new Error('Réponse serveur invalide: ' + text.substring(0, 100));
        }
        if (result.success) {
    showAlert(result.message, 'success');
    setTimeout(() => resetForm(), 3000);
} else {
    // Si c'est juste des doublons, afficher un message spécifique
    if (result.errors && result.errors.some(err => err.includes('existe déjà'))) {
        showAlert('Articles déjà existants : ' + result.errors.join(', '), 'warning');
    } else {
        showAlert(result.message, 'danger');
    }
    document.getElementById('confirmBtn').disabled = false;
}
 
    } catch (error) {
        console.error('Erreur complète:', error);
        showAlert('Erreur: ' + error.message, 'danger');
        document.getElementById('confirmBtn').disabled = false;
    }
}

// Réinitialiser le formulaire
function resetForm() {
    document.getElementById('fileInput').value = '';
    document.getElementById('selectedFile').classList.remove('show');
    document.getElementById('previewContainer').classList.remove('show');
    document.getElementById('summaryCards').style.display = 'none';
    document.getElementById('progressContainer').style.display = 'none';
    document.getElementById('processBtn').disabled = true;
    document.getElementById('confirmBtn').disabled = true;
    processedData = [];
    clearAlerts();
    updateProgress(0);
}

// Fonctions utilitaires
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function updateProgress(percent) {
    document.getElementById('progressFill').style.width = percent + '%';
    document.getElementById('progressText').textContent = `Traitement en cours... ${percent}%`;
}

function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <i class='bx ${type === 'success' ? 'bx-check-circle' : type === 'danger' ? 'bx-error-circle' : 'bx-info-circle'}'></i>
        ${message}
    `;
    alertContainer.appendChild(alert);
    
    // Auto-supprimer après 5 secondes
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 5000);
}

function clearAlerts() {
    document.getElementById('alertContainer').innerHTML = '';
}
</script>

</body>
</html>