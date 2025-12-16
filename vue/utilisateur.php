<?php
include 'entete.php';
include '../model/role_functions.php'; // Inclure les fonctions de rôles

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$utilisateurs = getUtilisateur();
$roles = getRoles(); // Récupérer la liste des rôles

// Stats
$totalUsers = count($utilisateurs);
$adminCount = 0;
$userCount = 0;

foreach ($utilisateurs as $user) {
    if ($user['role'] == 'admin') $adminCount++;
    if ($user['role'] == 'utilisateur') $userCount++;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
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
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--red-primary);
            color: var(--red-primary);
        }
        .btn-outline:hover {
            background-color: var(--red-primary);
            color: white;
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
            padding: 16px; 
            border-radius: 12px;
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
        
        /* Dynamic badge coloring could be improved, but simplistic for now */
        .badge-admin { background-color: #FEF3C7; color: #D97706; }
        .badge-user { background-color: #E5E7EB; color: #4B5563; }
        .badge-other { background-color: #DBEAFE; color: #1E40AF; }
        
        .actions-cell {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
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
        }
        
        .action-btn:hover {
            background: var(--red-primary);
            color: white;
        }
        
        .action-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
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
        }

        .form-control:focus {
            border-color: var(--red-primary);
            background-color: white;
            outline: none;
            box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
        }
    </style>
</head>
<body>

<div class="home-content">
    <div class="header-section">
        <h2 class="page-title">Gestion des Utilisateurs</h2>
        <div style="display: flex; gap: 10px;">
            <a href="roles.php" class="btn btn-outline" style="text-decoration: none;" data-tooltip="Gérer les types d'accès et permissions">
                <i class='bx bx-shield'></i> Gérer les Rôles
            </a>
            <button id="btn-nouveau" class="btn btn-success" data-tooltip="Créer un nouveau compte utilisateur">
                <i class='bx bx-user-plus'></i> Nouvel Utilisateur
            </button>
        </div>
    </div>
    
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-title">Total Utilisateurs</div>
            <div class="summary-value"><?= $totalUsers ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Administrateurs</div>
            <div class="summary-value" style="color: var(--red-primary);"><?= $adminCount ?></div>
        </div>
        <div class="summary-card">
            <div class="summary-title">Utilisateurs Standard</div>
            <div class="summary-value" style="color: var(--text-medium);"><?= $userCount ?></div>
        </div>
    </div>
    
    <?php if (!empty($_SESSION['message']['text'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 500; background-color: <?= $_SESSION['message']['type'] == 'success' ? '#D1FAE5' : '#FEE2E2' ?>; color: <?= $_SESSION['message']['type'] == 'success' ? '#065F46' : '#991B1B' ?>;">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <div class="search-container">
        <i class='bx bx-search' style="font-size: 1.2rem; color: var(--text-muted);"></i>
        <input type="text" id="searchInput" class="search-input" placeholder="Rechercher un utilisateur par nom ou email...">
    </div>
    
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Date Création</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <?php if (!empty($utilisateurs)): ?>
                    <?php foreach ($utilisateurs as $user): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <?php $color = stringToColor($user['nom'] . $user['prenom']); ?>
                                    <div class="user-avatar" style="background-color: <?= $color ?>;">
                                        <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
                                    </div>
                                    <div style="display: flex; flex-direction: column;">
                                        <span style="font-weight: 600; color: var(--text-dark);"><?= $user['nom'] ?> <?= $user['prenom'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="user-email-cell"><?= $user['email'] ?></td>
                            <td>
                                <?php 
                                    $badgeClass = 'badge-user';
                                    if ($user['role'] == 'admin') $badgeClass = 'badge-admin';
                                    elseif ($user['role'] != 'utilisateur') $badgeClass = 'badge-other';
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($user['date_creation'])) ?></td>
                            <td>
                                <div class="actions-cell">
                                    <a href="javascript:void(0);" onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)" class="action-btn" data-tooltip="Modifier les informations de l'utilisateur">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                    
                                    <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                    <a href="../controller/supprimerUtilisateur.php?id=<?= $user['id'] ?>" class="action-btn" data-tooltip="Supprimer cet utilisateur" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                        <i class='bx bx-trash'></i>
                                    </a>
                                    <?php else: ?>
                                    <span class="action-btn action-disabled" data-tooltip="Action non autorisée sur votre propre compte">
                                        <i class='bx bx-block'></i>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-medium);">
                            <i class='bx bx-user-x' style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                            Aucun utilisateur trouvé
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout/Modif (Using Global Red Motion Styles) -->
<div id="modal-user" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-titre" class="modal-title"><i class='bx bx-user-plus'></i> Nouvel Utilisateur</h3>
            <button id="btn-fermer-modal" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form id="form-user" action="../controller/ajoutUtilisateur.php" method="post">
                <input type="hidden" name="id" id="userId">
                
                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" id="nom" class="form-control" placeholder="Nom de famille" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" id="prenom" class="form-control" placeholder="Prénom" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="adresse@email.com" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Mot de passe <span id="pwd-hint" style="font-size: 0.8em; color: var(--text-muted); font-weight: normal;"></span></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Rôle</label>
                    <select name="role" id="role" class="form-control" required>
                        <?php foreach($roles as $role): ?>
                            <option value="<?= htmlspecialchars($role['nom']) ?>"><?= ucfirst(htmlspecialchars($role['nom'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" id="btn-fermer" class="btn btn-secondary" data-tooltip="Annuler l'opération">Annuler</button>
                    <button type="submit" class="btn btn-primary" data-tooltip="Enregistrer l'utilisateur">Enregistrer</button>
                </div>
            </form>
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
    const modal = document.getElementById('modal-user');
    const btnNouveau = document.getElementById('btn-nouveau');
    const btnFermer = document.getElementById('btn-fermer');
    const btnFermerX = document.getElementById('btn-fermer-modal');
    const titreModal = document.getElementById('modal-titre');
    const form = document.getElementById('form-user');
    const pwdHint = document.getElementById('pwd-hint');
    const pwdInput = document.getElementById('password');

    function ouvrirModal() {
        modal.style.display = 'block';
    }

    function fermerModal() {
        modal.style.display = 'none';
    }

    btnNouveau.onclick = function() {
        form.reset();
        form.action = "../controller/ajoutUtilisateur.php";
        document.getElementById('userId').value = '';
        titreModal.innerHTML = "<span class='modal-title-icon' style='background:var(--red-primary); color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-user-plus'></i></span> Nouvel Utilisateur";
        pwdHint.textContent = "(Requis)";
        pwdInput.required = true;
        ouvrirModal();
    }

    btnFermer.onclick = fermerModal;
    btnFermerX.onclick = fermerModal;

    window.onclick = function(event) {
        if (event.target == modal) fermerModal();
    }

    function editUser(user) {
        form.action = "../controller/modifUtilisateur.php";
        document.getElementById('userId').value = user.id;
        titreModal.innerHTML = "<span class='modal-title-icon' style='background:var(--red-primary); color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-edit'></i></span> Modifier Utilisateur";
        
        document.getElementById('nom').value = user.nom;
        document.getElementById('prenom').value = user.prenom;
        document.getElementById('email').value = user.email;
        document.getElementById('role').value = user.role;
        
        pwdHint.textContent = "(Laisser vide pour ne pas changer)";
        pwdInput.required = false;
        
        ouvrirModal();
    }
    
    // Search functionality for Table
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableBody = document.getElementById('usersTableBody');
        const rows = tableBody.getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            // Skip empty state row if present
            if(row.cells.length < 2) continue;
            
            const name = row.cells[0].textContent.toLowerCase();
            const email = row.cells[1].textContent.toLowerCase();
            
            if (name.includes(searchValue) || email.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
</script>

<?php include 'pied.php'; ?>
</body>
</html>