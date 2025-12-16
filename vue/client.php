<?php
include 'entete.php';

// Traitement pour la suppression d'un client
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $sql = "DELETE FROM client WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id]);
        
        $_SESSION['message']['text'] = "Client supprimé avec succès";
        $_SESSION['message']['type'] = "success";
        
        // Redirection pour éviter les soumissions multiples
        echo "<script>window.location.href = 'client.php';</script>";
        exit();
    } catch (Exception $e) {
        $_SESSION['message']['text'] = "Erreur lors de la suppression: " . $e->getMessage();
        $_SESSION['message']['type'] = "danger";
    }
}

$clients = getClient();

// Stats
$totalClients = count($clients);
$permisCount = 0;

foreach ($clients as $c) {
    if (!empty($c['permis_conduire'])) $permisCount++;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Clients</title>
    <style>
        /* Styles adaptés de employes.php (Patisserie Theme) */
        :root {
            --primary-color: #5D4037; /* Patisserie Theme */
            --secondary-color: #D4AF37;
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
        
        .home-content {
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
        
        .action-view { background-color: #17a2b8; }
        .action-edit { background-color: var(--secondary-color); }
        .action-delete { background-color: var(--accent-color); }
        
        /* Modal */
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
            max-width: 800px;
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
            background: linear-gradient(135deg, var(--primary-color), #8D6E63);
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
        
        .modal-body { 
            padding: 25px; 
            background-color: #f8f9fa;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
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
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
            outline: none;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
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
        
        .client-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        /* Info Grid Layout for Details */
        .info-section {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            height: 100%;
        }

        .section-title {
            color: var(--secondary-color);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 1rem;
            color: #2c3e50;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="home-content">
    <div class="header-section">
        <h2 class="page-title">Gestion des Clients</h2>
        <button id="btn-nouveau" class="btn btn-success">
            <i class='bx bx-user-plus'></i> Nouveau Client
        </button>
    </div>
    
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-title">Total Clients</div>
            <div class="summary-value"><?= $totalClients ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Avec Permis</div>
            <div class="summary-value" style="color: #2a9d8f;"><?= $permisCount ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Sans Permis</div>
            <div class="summary-value" style="color: #e76f51;"><?= $totalClients - $permisCount ?></div>
        </div>
    </div>
    
    <?php if (!empty($_SESSION['message']['text'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 5px; background-color: <?= $_SESSION['message']['type'] == 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message']['type'] == 'success' ? '#155724' : '#721c24' ?>;">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <div class="card" style="padding: 15px; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class='bx bx-search' style="font-size: 1.2rem; color: #666;"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un client..." style="border: none; background: transparent; padding: 5px;">
        </div>
    </div>
    
    <div class="data-table-wrapper">
        <table class="data-table" id="clientsTable">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Téléphone</th>
                    <th>CIN</th>
                    <th>Permis</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($clients)): ?>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php
                                    $color = stringToColor($client['nom'] . $client['prenom']);
                                    ?>
                                    <div class="client-avatar" style="background-color: <?= $color ?>;">
                                        <?= strtoupper(substr($client['prenom'], 0, 1) . substr($client['nom'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <strong><?= $client['nom'] ?> <?= $client['prenom'] ?></strong>
                                        <div style="font-size: 0.85em; color: #666;"><?= $client['email'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= $client['telephone'] ?></td>
                            <td><?= $client['cin'] ?></td>
                            <td><?= $client['permis_conduire'] ?></td>
                            <td>
                                <div class="actions-cell">
                                    <a href="javascript:void(0);" onclick="voirDetails(<?= htmlspecialchars(json_encode($client)) ?>)" class="action-btn action-view" title="Voir fiche">
                                        <i class='bx bx-show'></i>
                                    </a>
                                    <a href="javascript:void(0);" onclick="editClient(<?= htmlspecialchars(json_encode($client)) ?>)" class="action-btn action-edit" title="Modifier">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                    <a href="?action=supprimer&id=<?= $client['id'] ?>" class="action-btn action-delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                                        <i class='bx bx-trash'></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 20px;">Aucun client trouvé</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout/Modif -->
<div id="modal-client" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-titre" class="modal-title"><i class='bx bx-user-plus'></i> Nouveau Client</h3>
            <button id="btn-fermer-modal" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form id="form-client" action="../model/ajoutClient.php" method="post">
                <input type="hidden" name="id" id="clientId">
                
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
                        <label class="form-label"><i class='bx bx-phone'></i> Téléphone *</label>
                        <input type="text" name="telephone" id="telephone" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-envelope'></i> Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-id-card'></i> CIN</label>
                        <input type="text" name="cin" id="cin" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-car'></i> Permis de conduire</label>
                        <input type="text" name="permis_conduire" id="permis_conduire" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-calendar'></i> Date de naissance</label>
                        <input type="date" name="date_naissance" id="date_naissance" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class='bx bx-map'></i> Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="form-control">
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label"><i class='bx bx-comment-detail'></i> Commentaires</label>
                        <textarea name="commentaires" id="commentaires" class="form-control"></textarea>
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

<!-- Modal Détails Client -->
<div id="modal-details" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 class="modal-title"><i class='bx bx-id-card'></i> Fiche Client</h3>
            <button id="btn-fermer-details" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body" style="padding: 0;">
            <div id="details-content">
                <!-- Content will be populated by JS -->
            </div>
        </div>
    </div>
</div>

<?php
function stringToColor($str) {
    $hash = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $hash = ord($str[$i]) + (($hash << 5) - $hash);
    }
    $color = '#';
    for ($i = 0; $i < 3; $i++) {
        $value = ($hash >> ($i * 8)) & 0xFF;
        $color .= str_pad(dechex($value), 2, '0', STR_PAD_LEFT);
    }
    return $color;
}
?>

<script>
    const modal = document.getElementById('modal-client');
    const modalDetails = document.getElementById('modal-details');
    const btnNouveau = document.getElementById('btn-nouveau');
    const btnFermer = document.getElementById('btn-fermer');
    const btnFermerX = document.getElementById('btn-fermer-modal');
    const btnFermerDetails = document.getElementById('btn-fermer-details');
    const titreModal = document.getElementById('modal-titre');
    const form = document.getElementById('form-client');

    function ouvrirModal() {
        modal.style.display = 'block';
    }

    function fermerModal() {
        modal.style.display = 'none';
    }
    
    function fermerModalDetails() {
        modalDetails.style.display = 'none';
    }

    btnNouveau.onclick = function() {
        form.reset();
        form.action = "../model/ajoutClient.php";
        document.getElementById('clientId').value = '';
        titreModal.innerHTML = "<i class='bx bx-user-plus'></i> Nouveau Client";
        ouvrirModal();
    }

    btnFermer.onclick = fermerModal;
    btnFermerX.onclick = fermerModal;
    btnFermerDetails.onclick = fermerModalDetails;

    window.onclick = function(event) {
        if (event.target == modal) fermerModal();
        if (event.target == modalDetails) fermerModalDetails();
    }

    function editClient(client) {
        form.action = "../model/modifClient.php";
        document.getElementById('clientId').value = client.id;
        titreModal.innerHTML = "<i class='bx bx-edit'></i> Modifier Client";
        
        document.getElementById('nom').value = client.nom;
        document.getElementById('prenom').value = client.prenom;
        document.getElementById('telephone').value = client.telephone;
        document.getElementById('email').value = client.email;
        document.getElementById('cin').value = client.cin;
        document.getElementById('permis_conduire').value = client.permis_conduire;
        document.getElementById('date_naissance').value = client.date_naissance;
        document.getElementById('adresse').value = client.adresse;
        document.getElementById('commentaires').value = client.commentaires;
        
        ouvrirModal();
    }
    
    function voirDetails(data) {
        const content = document.getElementById('details-content');
        
        // Generate avatar color
        let hash = 0;
        let str = data.nom + data.prenom;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        let color = '#';
        for (let i = 0; i < 3; i++) {
            let value = (hash >> (i * 8)) & 0xFF;
            color += ('00' + value.toString(16)).substr(-2);
        }
        
        const initials = (data.prenom.charAt(0) + data.nom.charAt(0)).toUpperCase();
        
        content.innerHTML = `
            <div style="position: relative; background: #f8f9fa; padding-bottom: 20px;">
                <div style="height: 100px; background: linear-gradient(135deg, var(--primary-color), #8D6E63); border-radius: 0 0 50% 50% / 20px;"></div>
                <div style="margin-top: -60px; display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 120px; height: 120px; border-radius: 50%; background-color: ${color}; display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2); color: white; font-size: 40px; font-weight: bold;">
                        ${initials}
                    </div>
                    <h2 style="margin: 15px 0 5px 0; color: var(--primary-color);">${data.nom} ${data.prenom}</h2>
                    <span style="color: #666;">${data.email || ''}</span>
                </div>
            </div>
            
            <div style="padding: 30px; background: #f8f9fa;">
                <div class="info-section">
                    <h4 class="section-title"><i class='bx bx-user-pin'></i> Informations Personnelles</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Téléphone</span>
                            <span class="info-value">${data.telephone || '-'}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">CIN</span>
                            <span class="info-value">${data.cin || '-'}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Permis de conduire</span>
                            <span class="info-value">${data.permis_conduire || '-'}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date de Naissance</span>
                            <span class="info-value">${data.date_naissance || '-'}</span>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <span class="info-label">Adresse</span>
                            <span class="info-value">${data.adresse || '-'}</span>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <span class="info-label">Commentaires</span>
                            <span class="info-value">${data.commentaires || '-'}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        modalDetails.style.display = 'block';
    }
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('clientsTable');
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent || cells[j].innerText;
                if (cellText.toLowerCase().indexOf(searchValue) > -1) {
                    found = true;
                    break;
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    });
</script>

<?php include 'pied.php'; ?>
</body>
</html>