<?php
include 'entete.php';
require_once '../model/hr_functions.php';

$userId = $_SESSION['user']['id'];
$employe = getEmploye($userId);
$documents = getDocumentsByEmploye($userId);

// If employee not found (e.g. admin who is not an employee), handle gracefully
if (!$employe) {
    // Basic fallback if not in employes table
    $employe = $_SESSION['user']; 
}
?>

<style>
    .home-content {
        padding: 24px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header Section (Matched to Utilisateur) */
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
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: var(--red-primary);
        font-size: 1.8rem;
    }
    
    /* Layout */
    .profile-layout {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 992px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }
    }

    /* Cards (Matched to Summary Cards styling) */
    .card {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--border-light);
        overflow: hidden;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-body {
        padding: 24px;
    }

    /* User Profile Specifics */
    .user-card-header {
        background: linear-gradient(135deg, var(--red-primary), var(--red-dark));
        height: 120px;
        position: relative;
    }

    .profile-avatar-wrapper {
        position: relative;
        margin-top: -60px;
        text-align: center;
        margin-bottom: 16px;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: white;
        border: 4px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: 800;
        color: var(--red-primary);
    }

    .profile-info-center {
        text-align: center;
        margin-bottom: 24px;
    }

    .profile-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 4px;
    }

    .profile-role {
        display: inline-block;
        padding: 4px 12px;
        background: #FEF2F2;
        color: var(--red-primary);
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .info-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 0;
        border-bottom: 1px solid var(--border-light);
    }

    .info-item:last-child { border-bottom: none; }

    .info-label {
        color: var(--text-medium);
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-label i {
        color: var(--text-light);
        font-size: 1.1rem;
    }

    .info-value {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.95rem;
    }

    /* Buttons (Matched to Utilisateur) */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }
    
    .btn-primary { 
        background-color: var(--red-primary); 
        color: white; 
        box-shadow: 0 4px 6px rgba(230, 57, 70, 0.2);
    }
    .btn-primary:hover { 
        background-color: var(--red-dark); 
        transform: translateY(-1px);
    }
    
    .btn-secondary { 
        background-color: white; 
        color: var(--text-medium); 
        border: 1px solid var(--border-medium); 
    }
    .btn-secondary:hover { 
        background-color: var(--bg-light); 
        border-color: var(--text-muted);
    }
    
    .btn-full { width: 100%; justify-content: center; }

    /* Documents Grid */
    .docs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .doc-card {
        background: white;
        border: 1px solid var(--border-light);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        transition: all 0.3s;
        position: relative;
    }

    .doc-card:hover {
        border-color: var(--red-light);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }

    .doc-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }

    .doc-icon.pdf { background: #fee2e2; color: #ef4444; } /* Red tint */
    .doc-icon.img { background: #dcfce7; color: #22c55e; } /* Green tint */
    .doc-icon.default { background: #f3f4f6; color: #4b5563; } /* Grey tint */

    .doc-info {
        flex: 1;
        min-width: 0;
    }

    .doc-type {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: var(--text-medium);
        margin-bottom: 4px;
        display: block;
    }

    .doc-title {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.95rem;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    .doc-date {
        font-size: 0.8rem;
        color: var(--text-light);
    }

    .doc-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }

    .action-icon-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-medium);
        background: var(--bg-light);
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    .action-icon-btn:hover { background: var(--text-medium); color: white; }
    .action-icon-btn.delete:hover { background: #EF4444; color: white; }
    .action-icon-btn.download:hover { background: var(--red-primary); color: white; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--bg-light);
        border-radius: 12px;
        border: 1px dashed var(--border-medium);
    }

    /* Modal Styles from Utilisateur */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1000; 
        left: 0; 
        top: 0; 
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; /* 5% from the top and centered */
        padding: 0;
        border: 1px solid #888;
        width: 100%;
        max-width: 500px; /* Matched width */
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
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
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-close {
        background: transparent;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--text-medium);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .modal-close:hover { background: var(--bg-light); color: var(--text-dark); }

    .modal-body { padding: 24px; }

    .modal-footer {
        padding: 20px 24px;
        background-color: var(--bg-light);
        border-top: 1px solid var(--border-light);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        border-radius: 0 0 16px 16px;
    }

    /* Form Fields from Utilisateur */
    .form-group { margin-bottom: 20px; }
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
    .form-control:focus {
        border-color: var(--red-primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
    }
    .form-helper {
        font-size: 0.8rem;
        color: var(--text-light);
        margin-top: 6px;
        display: block;
    }

    .alert {
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-success { background: #ecfdf5; color: #047857; }
    .alert-error { background: #fef2f2; color: #b91c1c; }
</style>

<div class="home-content">
    <div class="header-section">
        <h2 class="page-title"><i class='bx bxs-user-detail'></i> Mon Profil</h2>
        <!-- Optional: Breadcrumb or extra actions could go here -->
    </div>

    <!-- Alert Messages (Same as Utilisateur) -->
    <?php if (!empty($_SESSION['message']['text'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?>">
            <i class='bx bx-info-circle' style="font-size: 1.25rem;"></i>
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="profile-layout">
        
        <!-- Left Column: User Card -->
        <div class="card">
            <div class="user-card-header"></div>
            <div class="card-body" style="padding-top: 0;">
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($employe['prenom'] ?? 'U', 0, 1) . substr($employe['nom'] ?? 'S', 0, 1)) ?>
                    </div>
                </div>
                
                <div class="profile-info-center">
                    <h3 class="profile-name"><?= htmlspecialchars(($employe['prenom'] ?? '') . ' ' . ($employe['nom'] ?? '')) ?></h3>
                    <div class="profile-role"><?= htmlspecialchars($employe['poste'] ?? $employe['role'] ?? 'Employé') ?></div>
                </div>

                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label"><i class='bx bx-id-card'></i> Matricule</span>
                        <span class="info-value">EMP-<?= str_pad($employe['id'], 3, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class='bx bx-envelope'></i> Email</span>
                        <span class="info-value"><?= htmlspecialchars($employe['email'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class='bx bx-phone'></i> Téléphone</span>
                        <span class="info-value"><?= htmlspecialchars($employe['telephone'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class='bx bx-building-house'></i> Département</span>
                        <span class="info-value"><?= htmlspecialchars($employe['departement'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class='bx bx-calendar-check'></i> Embauche</span>
                        <span class="info-value"><?= !empty($employe['date_embauche']) ? date('d/m/Y', strtotime($employe['date_embauche'])) : '-' ?></span>
                    </div>
                </div>

                <div style="margin-top: 32px;">
                    <button onclick="openModal('editProfileModal')" class="btn btn-secondary btn-full">
                        <i class='bx bx-edit-alt'></i> Modifier mes informations
                    </button>
                    <div style="margin-top: 12px; text-align: center;">
                        <button onclick="openModal('changePasswordModal')" class="btn btn-secondary btn-full" style="border-style: dashed; color: var(--text-light);">
                            <i class='bx bx-lock-alt'></i> Changer le mot de passe
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Documents -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <span style="background: rgba(230, 57, 70, 0.1); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: var(--red-primary);">
                        <i class='bx bx-folder'></i>
                    </span>
                    Documents Administratifs
                </div>
                <button onclick="openModal('addDocModal')" class="btn btn-primary">
                    <i class='bx bx-upload'></i> Ajouter un document
                </button>
            </div>
            <div class="card-body">
                
                <?php if(empty($documents)): ?>
                    <div class="empty-state">
                        <div style="width: 80px; height: 80px; background: var(--bg-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--text-light);">
                            <i class='bx bx-folder-open' style="font-size: 40px;"></i>
                        </div>
                        <h4 style="margin: 0 0 8px 0; color: var(--text-dark);">Aucun document</h4>
                        <p style="margin: 0; color: var(--text-medium); font-size: 0.9rem;">Vous n'avez pas encore téléversé de documents.</p>
                    </div>
                <?php else: ?>
                    <div class="docs-grid">
                        <?php foreach ($documents as $doc): 
                            $ext = strtolower(pathinfo($doc['fichier'], PATHINFO_EXTENSION));
                            $iconClass = 'default';
                            $icon = 'bx-file';

                            if($ext == 'pdf') { $iconClass = 'pdf'; $icon = 'bxs-file-pdf'; }
                            elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) { $iconClass = 'img'; $icon = 'bxs-image'; }
                        ?>
                        <div class="doc-card">
                            <div class="doc-doc-content" style="display: flex; gap: 16px; width: 100%;">
                                <div class="doc-icon <?= $iconClass ?>">
                                    <i class='bx <?= $icon ?>'></i>
                                </div>
                                <div class="doc-info">
                                    <span class="doc-type"><?= htmlspecialchars($doc['type']) ?></span>
                                    <span class="doc-title" title="<?= htmlspecialchars($doc['titre']) ?>"><?= htmlspecialchars($doc['titre']) ?></span>
                                    <span class="doc-date">Ajouté le <?= date('d/m/Y', strtotime($doc['date_ajout'])) ?></span>
                                    
                                    <div class="doc-actions">
                                        <?php 
                                            // Link to the blob viewer
                                            $downloadLink = '../controller/view_document.php?id=' . $doc['id'];
                                        ?>
                                        <a href="<?= $downloadLink ?>" target="_blank" class="action-icon-btn download" title="Télécharger / Voir">
                                            <i class='bx bx-download'></i>
                                        </a>
                                        <form action="../controller/profile_controller.php" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" style="margin: 0;">
                                            <input type="hidden" name="delete_document" value="1">
                                            <input type="hidden" name="id_document" value="<?= $doc['id'] ?>">
                                            <button type="submit" class="action-icon-btn delete" title="Supprimer">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<!-- Modal Add Doc -->
<div id="addDocModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <span style="background: var(--red-primary); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class='bx bx-upload'></i>
                </span>
                Ajouter un document
            </h3>
            <button class="modal-close" onclick="closeModal('addDocModal')"><i class='bx bx-x'></i></button>
        </div>
        <form action="../controller/profile_controller.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="upload_document" value="1">
                
                <div class="form-group">
                    <label class="form-label">Catégorie</label>
                    <select name="type" class="form-control" required>
                        <option value="CIN">Carte d'Identité (CIN)</option>
                        <option value="Contrat">Contrat de Travail</option>
                        <option value="Diplome">Diplômes / Certifications</option>
                        <option value="CV">CV</option>
                        <option value="Rib">RIB Bancaire</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Titre du document</label>
                    <input type="text" name="titre" class="form-control" placeholder="Ex: CIN Recto-Verso" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Fichier</label>
                    <div style="position: relative;">
                        <input type="file" name="fichier" class="form-control" style="padding: 10px; border: 2px dashed var(--border-medium); background: var(--bg-light); text-align: center;" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" required>
                    </div>
                    <small class="form-helper">Formats acceptés: PDF, Images (JPG, PNG), Word.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addDocModal')">Annuler</button>
                <button type="submit" class="btn btn-primary">Télécharger</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Info -->
<div id="editProfileModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <span style="background: var(--red-primary); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class='bx bx-edit-alt'></i>
                </span>
                Modifier mes informations
            </h3>
            <button class="modal-close" onclick="closeModal('editProfileModal')"><i class='bx bx-x'></i></button>
        </div>
        <form action="../controller/updateProfile.php" method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="update_info">
                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($employe['nom'] ?? '') ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($employe['prenom'] ?? '') ?>" class="form-control">
                </div>
                <div class="alert alert-error" style="margin-bottom: 0; padding: 12px; font-size: 13px;">
                    <i class='bx bx-info-circle'></i> Pour modifier d'autres informations (Email, Téléphone, etc.), veuillez contacter les RH.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editProfileModal')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Change Password -->
<div id="changePasswordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <span style="background: var(--text-dark); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class='bx bx-lock-alt'></i>
                </span>
                Changer mot de passe
            </h3>
            <button class="modal-close" onclick="closeModal('changePasswordModal')"><i class='bx bx-x'></i></button>
        </div>
        <form action="../controller/updateProfile.php" method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="update_password">
                <div class="form-group">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="new_password" placeholder="••••••••" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('changePasswordModal')">Annuler</button>
                <button type="submit" class="btn btn-primary" style="background: var(--text-dark);">Changer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
    }
    
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
        // Reset forms if needed
    }
    
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

<?php include 'pied.php'; ?>
