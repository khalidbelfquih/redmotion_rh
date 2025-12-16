<?php
include 'entete.php';
include '../model/role_functions.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$roles = getRoles();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Rôles</title>
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
        <div>
            <h2 class="page-title">Gestion des Rôles</h2>
            <div style="margin-top: 5px;">
                <a href="utilisateur.php" style="color: var(--red-primary); text-decoration: none; font-size: 14px;"><i class='bx bx-arrow-back'></i> Retour Utilisateurs</a>
            </div>
        </div>
        <button id="btn-nouveau" class="btn btn-success">
            <i class='bx bx-plus'></i> Nouveau Rôle
        </button>
    </div>
    
    <?php if (!empty($_SESSION['message']['text'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 500; background-color: <?= $_SESSION['message']['type'] == 'success' ? '#D1FAE5' : '#FEE2E2' ?>; color: <?= $_SESSION['message']['type'] == 'success' ? '#065F46' : '#991B1B' ?>;">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom du Rôle</th>
                    <th>Description</th>
                    <th>Date Création</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($roles)): ?>
                    <?php foreach ($roles as $role): ?>
                        <tr>
                            <td>
                                <span style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($role['nom']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($role['description']) ?></td>
                            <td><?= date('d/m/Y', strtotime($role['date_creation'])) ?></td>
                            <td>
                                <div class="actions-cell">
                                    <a href="javascript:void(0);" onclick="openPermissionsModal(<?= $role['id'] ?>, '<?= htmlspecialchars($role['nom']) ?>')" class="action-btn" title="Gérer Accès Menu" style="background: #E0F2FE; color: #0284C7;">
                                        <i class='bx bx-list-check'></i>
                                    </a>

                                    <a href="javascript:void(0);" onclick="editRole(<?= htmlspecialchars(json_encode($role)) ?>)" class="action-btn" title="Modifier">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                    
                                    <a href="../controller/role_controller.php?action=delete&id=<?= $role['id'] ?>" class="action-btn" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?');">
                                        <i class='bx bx-trash'></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-medium);">
                            Aucun rôle trouvé
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout/Modif -->
<div id="modal-role" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-titre" class="modal-title"><i class='bx bx-shield'></i> Nouveau Rôle</h3>
            <button id="btn-fermer-modal" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <form id="form-role" action="../controller/role_controller.php" method="post">
                <input type="hidden" name="action" value="add" id="form-action">
                <input type="hidden" name="id" id="roleId">
                
                <div class="form-group">
                    <label class="form-label">Nom du rôle (Identifiant)</label>
                    <input type="text" name="nom" id="nom" class="form-control" placeholder="ex: manager, rh, commercial" required>
                    <small style="color: grey;">Utilisez des minuscules, sans espaces (ex: admin, manager).</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" placeholder="Description du rôle..." rows="3"></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" id="btn-fermer" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Permissions -->
<div id="modal-permissions" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">
                <span style="background: #0EA5E9; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class='bx bx-list-check'></i>
                </span>
                Accès Menu : <span id="perm-role-name" style="margin-left: 8px; font-weight: 800;"></span>
            </h3>
            <button onclick="closePermissionsModal()" class="modal-close"><i class='bx bx-x'></i></button>
        </div>
        <div class="modal-body">
            <p style="color: var(--text-medium); font-size: 13px; margin-bottom: 20px;">Sélectionnez les éléments du menu visibles pour ce rôle.</p>
            <div id="permissions-loader" style="text-align: center; padding: 20px;">
                <i class='bx bx-loader-alt bx-spin' style="font-size: 24px; color: var(--red-primary);"></i>
            </div>
            <div id="permissions-list" style="display: none; display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <!-- Checkboxes populated by JS -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closePermissionsModal()" class="btn btn-secondary">Fermer</button>
            <button type="button" onclick="savePermissions()" class="btn btn-primary">Enregistrer Accès</button>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modal-role');
    const permModal = document.getElementById('modal-permissions');
    
    // Existing Modal Logic
    const btnNouveau = document.getElementById('btn-nouveau');
    const btnFermer = document.getElementById('btn-fermer');
    const btnFermerX = document.getElementById('btn-fermer-modal');
    const titreModal = document.getElementById('modal-titre');
    const form = document.getElementById('form-role');
    const formAction = document.getElementById('form-action');

    function ouvrirModal() {
        modal.style.display = 'block';
    }

    function fermerModal() {
        modal.style.display = 'none';
    }

    btnNouveau.onclick = function() {
        form.reset();
        formAction.value = 'add';
        document.getElementById('roleId').value = '';
        titreModal.innerHTML = "<span class='modal-title-icon' style='background:var(--red-primary); color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-plus'></i></span> Nouveau Rôle";
        ouvrirModal();
    }

    btnFermer.onclick = fermerModal;
    btnFermerX.onclick = fermerModal;

    window.onclick = function(event) {
        if (event.target == modal) fermerModal();
        if (event.target == permModal) closePermissionsModal();
    }

    function editRole(role) {
        formAction.value = 'edit';
        document.getElementById('roleId').value = role.id;
        titreModal.innerHTML = "<span class='modal-title-icon' style='background:var(--red-primary); color:white; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; margin-right:10px;'><i class='bx bx-edit'></i></span> Modifier Rôle";
        
        document.getElementById('nom').value = role.nom;
        document.getElementById('description').value = role.description;
        
        ouvrirModal();
    }
    
    // --- Permissions Logic ---
    let currentRoleId = null;

    function openPermissionsModal(roleId, roleName) {
        currentRoleId = roleId;
        document.getElementById('perm-role-name').innerText = roleName;
        permModal.style.display = 'block';
        
        const loader = document.getElementById('permissions-loader');
        const list = document.getElementById('permissions-list');
        
        loader.style.display = 'block';
        list.style.display = 'none';
        list.innerHTML = '';
        
        fetch(`../controller/api_role_permissions.php?role_id=${roleId}`)
            .then(res => res.json())
            .then(data => {
                loader.style.display = 'none';
                list.style.display = 'grid'; // Restore grid
                
                if (data.error) {
                    list.innerHTML = `<div style="color:red; grid-column: 1/-1;">${data.error}</div>`;
                    return;
                }
                
                data.forEach(item => {
                    const checked = item.allowed ? 'checked' : '';
                    const itemHtml = `
                        <label style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-light); border-radius: 8px; cursor: pointer; border: 1px solid transparent; transition: all 0.2s;" class="perm-item">
                            <input type="checkbox" class="perm-checkbox" value="${item.id}" ${checked} style="width: 18px; height: 18px; accent-color: var(--red-primary);">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class='${item.icon}' style="font-size: 18px; color: var(--red-primary);"></i>
                                <span style="font-weight: 500; font-size: 14px;">${item.label}</span>
                            </div>
                        </label>
                    `;
                    list.innerHTML += itemHtml;
                });
                
                // Add hover effect listeners
                document.querySelectorAll('.perm-item').forEach(el => {
                    const cb = el.querySelector('input');
                    el.addEventListener('click', () => { setTimeout(() => {
                        el.style.borderColor = cb.checked ? 'var(--red-primary)' : 'transparent';
                        el.style.background = cb.checked ? '#FEF2F2' : 'var(--bg-light)';
                    }, 0)});
                    // Init state
                    if(cb.checked) {
                        el.style.borderColor = 'var(--red-primary)';
                        el.style.background = '#FEF2F2';
                    }
                });
            })
            .catch(err => {
                console.error(err);
                loader.style.display = 'none';
                list.innerHTML = `<div style="color:red;">Erreur de chargement</div>`;
                list.style.display = 'block';
            });
    }
    
    function closePermissionsModal() {
        permModal.style.display = 'none';
    }
    
    function savePermissions() {
        if (!currentRoleId) return;
        
        const checkboxes = document.querySelectorAll('.perm-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value);
        
        const btn = document.querySelector('#modal-permissions .btn-primary');
        const oldText = btn.innerText;
        btn.innerText = 'Enregistrement...';
        btn.disabled = true;
        
        fetch('../controller/api_role_permissions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ role_id: currentRoleId, menu_items: ids })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Show simplified toast mechanism since we don't have a global one easily accessible, 
                // or just alert and close
                closePermissionsModal();
                // Optional: reload if current user is affected, but that's complex
            } else {
                alert('Erreur: ' + (data.message || 'Inconnue'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erreur réseau');
        })
        .finally(() => {
            btn.innerText = oldText;
            btn.disabled = false;
        });
    }
</script>

<?php include 'pied.php'; ?>
</body>
</html>
