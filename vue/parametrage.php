<?php
include 'entete.php';

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'societe';
?>

<div class="home-content">
    
    <?php if (!empty($_SESSION['message']['text'])): ?>
        <div class="alert <?= $_SESSION['message']['type'] ?>" style="margin: 0 0 24px 0;">
            <i class='bx bx-info-circle' style="font-size: 1.2rem; margin-right: 10px;"></i>
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="settings-container">
        
        <div class="header-section">
            <div class="page-title-group">
                <div class="page-title-icon">
                    <i class='bx bx-cog'></i>
                </div>
                <div>
                   <h2 class="page-title">Paramétrage</h2>
                   <p class="page-subtitle">Gestion des configurations de l'application</p>
                </div>
            </div>
        </div>

        <div class="tabs-wrapper">
            
            <!-- Tabs Navigation -->
            <div class="tabs-nav">
                <a href="?tab=societe" class="tab-link <?= $activeTab == 'societe' ? 'active' : '' ?>">
                    <i class='bx bx-building'></i> Infos Société
                </a>
                <a href="?tab=conges" class="tab-link <?= $activeTab == 'conges' ? 'active' : '' ?>">
                    <i class='bx bx-calendar-minus'></i> Types de Congés
                </a>
                <a href="?tab=postes" class="tab-link <?= $activeTab == 'postes' ? 'active' : '' ?>">
                    <i class='bx bx-briefcase'></i> Types de Postes
                </a>
                <a href="?tab=feries" class="tab-link <?= $activeTab == 'feries' ? 'active' : '' ?>">
                    <i class='bx bx-calendar-star'></i> Jours Fériés
                </a>
                <a href="?tab=departements" class="tab-link <?= $activeTab == 'departements' ? 'active' : '' ?>">
                    <i class='bx bx-building-house'></i> Départements
                </a>
                <a href="?tab=events" class="tab-link <?= $activeTab == 'events' ? 'active' : '' ?>">
                    <i class='bx bx-calendar-event'></i> Types d'Évènements
                </a>
            </div>

            <!-- Tabs Content -->
            <div class="tab-content">
                
                <!-- 1. Infos Société -->
                <?php if ($activeTab == 'societe'): 
                    $stmt = $connexion->query("SELECT * FROM societe_info LIMIT 1");
                    $societe = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <form action="../controller/parametrage_controller.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_societe">
                    <input type="hidden" name="id" value="<?= $societe['id'] ?? '' ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nom de la société</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($societe['nom'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($societe['telephone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($societe['email'] ?? '') ?>">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Adresse</label>
                            <textarea name="adresse" class="form-control" rows="2"><?= htmlspecialchars($societe['adresse'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="divider full-width">Informations Légales</div>
                        
                        <div class="form-group">
                            <label class="form-label">R.C (Registre de Commerce)</label>
                            <input type="text" name="rc" class="form-control" value="<?= htmlspecialchars($societe['rc'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">I.C.E (Identifiant Commun de l'Entreprise)</label>
                            <input type="text" name="ice" class="form-control" value="<?= htmlspecialchars($societe['ice'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">I.F (Identifiant Fiscal)</label>
                            <input type="text" name="if_fiscal" class="form-control" value="<?= htmlspecialchars($societe['if_fiscal'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CNSS</label>
                            <input type="text" name="cnss" class="form-control" value="<?= htmlspecialchars($societe['cnss'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Patente (Taxe Professionnelle)</label>
                            <input type="text" name="art" class="form-control" value="<?= htmlspecialchars($societe['art'] ?? '') ?>">
                        </div>
                        
                        <div class="divider full-width">Documents</div>
                        
                        <div class="form-group">
                            <label class="form-label">Logo Société</label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="logo" accept="image/*" class="form-control">
                                <?php if(!empty($societe['logo_path'])): ?>
                                    <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px; border: 1px dashed #ced4da; display: inline-block;">
                                        <img src="<?= $societe['logo_path'] ?>" alt="Logo Actuel" style="max-height: 80px; max-width: 100%; object-fit: contain;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Cachet / Tampon</label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="cachet" accept="image/*" class="form-control">
                                <?php if(!empty($societe['cachet_path'])): ?>
                                    <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px; border: 1px dashed #ced4da; display: inline-block;">
                                        <img src="<?= $societe['cachet_path'] ?>" alt="Cachet Actuel" style="max-height: 80px; max-width: 100%; object-fit: contain;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="divider full-width">Paramètres Congés</div>

                        <div class="form-group">
                            <label class="form-label">Jours de congé par an (Solde annuel)</label>
                            <input type="number" name="jours_conge_annuel" class="form-control" value="<?= htmlspecialchars($societe['jours_conge_annuel'] ?? 18) ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Jours de Week-end (ne comptent pas dans les congés)</label>
                            <div class="weekend-days-selector">
                                <?php 
                                $weekendIndices = explode(',', $societe['weekend_days'] ?? '0'); // Default Sunday
                                $days = [
                                    1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 
                                    4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 0 => 'Dimanche'
                                ];
                                foreach ($days as $idx => $label): 
                                ?>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="weekend_days[]" value="<?= $idx ?>" <?= in_array($idx, $weekendIndices) ? 'checked' : '' ?>>
                                        <span class="custom-checkbox"></span>
                                        <?= $label ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div style="text-align: right;">
                        <button type="submit" class="btn btn-primary" data-tooltip="Sauvegarder les informations de la société"><i class='bx bx-save'></i> Enregistrer les modifications</button>
                    </div>
                </form>
                <?php endif; ?>


                <!-- 2. Types de Congés -->
                <?php if ($activeTab == 'conges'): ?>
                    <div class="list-header">
                        <h3>Liste des types de congés</h3>
                        <button class="btn btn-primary" onclick="openModal('modal-conge')" data-tooltip="Ajouter un nouveau type de congé"><i class='bx bx-plus'></i> Ajouter</button>
                    </div>
                    
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Durée</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stmt = $connexion->query("SELECT * FROM conge_type");
                                while($row = $stmt->fetch()): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($row['nom']) ?></td>
                                    <td><?= htmlspecialchars($row['duree'] ?? 0) ?> jours</td>
                                    <td style="color: var(--text-medium);"><?= htmlspecialchars($row['description']) ?></td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="#" onclick="editConge(<?= htmlspecialchars(json_encode($row)) ?>)" class="action-btn action-edit" data-tooltip="Modifier ce type de congé"><i class='bx bx-edit-alt'></i></a>
                                            <a href="../controller/parametrage_controller.php?action=delete_conge&id=<?= $row['id'] ?>" onclick="return confirm('Confirmer la suppression ?')" class="action-btn action-delete" data-tooltip="Supprimer ce type de congé"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Conge -->
                    <div id="modal-conge" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 id="modal-conge-title" class="modal-title">
                                    <span style="background: var(--red-primary); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                        <i class='bx bx-calendar-minus'></i>
                                    </span>
                                    Ajouter un type de congé
                                </h3>
                                <button class="modal-close" onclick="closeModal('modal-conge')"><i class='bx bx-x'></i></button>
                            </div>
                            <div class="modal-body">
                                <form action="../controller/parametrage_controller.php" method="post">
                                    <input type="hidden" name="action" value="save_conge">
                                    <input type="hidden" name="id" id="conge-id">
                                    
                                    <div class="form-group">
                                        <label class="form-label">Nom du congé</label>
                                        <input type="text" name="nom" id="conge-nom" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Durée (jours)</label>
                                        <input type="number" name="duree" id="conge-duree" class="form-control" min="0" value="0">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" id="conge-description" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-conge')">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Valider</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 3. Types de Postes -->
                <?php if ($activeTab == 'postes'): ?>
                    <div class="list-header">
                        <h3>Liste des types de postes</h3>
                        <button class="btn btn-primary" onclick="openModal('modal-poste')" data-tooltip="Ajouter un nouveau poste"><i class='bx bx-plus'></i> Ajouter</button>
                    </div>
                    
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Titre du Poste</th>
                                    <th>Département</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Fetch Postes with Department Name
                                $sql = "SELECT p.*, d.nom as departement_nom 
                                        FROM postes p 
                                        LEFT JOIN departements d ON p.id_departement = d.id 
                                        ORDER BY p.titre ASC";
                                $stmt = $connexion->query($sql);
                                while($row = $stmt->fetch()): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($row['titre']) ?></td>
                                    <td>
                                        <?php if($row['departement_nom']): ?>
                                            <span class="badge badge-info" style="background:var(--bg-light); color:var(--text-dark); border:1px solid var(--border-medium);"><?= htmlspecialchars($row['departement_nom']) ?></span>
                                        <?php else: ?>
                                            <span style="color:var(--text-muted); font-style:italic;">Non assigné</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: var(--text-medium);"><?= htmlspecialchars($row['description']) ?></td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="#" onclick="editPoste(<?= htmlspecialchars(json_encode($row)) ?>)" class="action-btn action-edit" data-tooltip="Modifier ce poste"><i class='bx bx-edit-alt'></i></a>
                                            <a href="../controller/parametrage_controller.php?action=delete_poste&id=<?= $row['id'] ?>" onclick="return confirm('Confirmer la suppression ?')" class="action-btn action-delete" data-tooltip="Supprimer ce poste"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Poste -->
                    <div id="modal-poste" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title" id="modal-poste-title">
                                    <span style="background: var(--red-primary); color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                        <i class='bx bx-briefcase'></i>
                                    </span>
                                    Nouveau Poste
                                </h3>
                                <button class="modal-close" onclick="closeModal('modal-poste')"><i class='bx bx-x'></i></button>
                            </div>
                            <div class="modal-body">
                                <form action="../controller/parametrage_controller.php" method="post">
                                    <input type="hidden" name="action" value="save_poste">
                                    <input type="hidden" name="id" id="poste_id">
                                    
                                    <div class="form-group">
                                        <label class="form-label">Titre du Poste</label>
                                        <input type="text" name="titre" id="poste_nom" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Département</label>
                                        <select name="id_departement" id="poste_dept" class="form-control">
                                            <option value="">-- Aucun --</option>
                                            <?php 
                                            // Provide department options
                                            $depts = $connexion->query("SELECT * FROM departements ORDER BY nom ASC")->fetchAll();
                                            foreach($depts as $dept): 
                                            ?>
                                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['nom']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" id="poste_desc" class="form-control" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-poste')">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


                <!-- 3. Jours Fériés -->
                <?php if ($activeTab == 'feries'): ?>
                    <div class="list-header">
                        <h3>Liste des jours fériés</h3>
                        <button class="btn btn-primary" onclick="openModal('modal-ferie')" data-tooltip="Ajouter un nouveau jour férié"><i class='bx bx-plus'></i> Ajouter</button>
                    </div>
                    
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Date</th>
                                    <th>Récurrent</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stmt = $connexion->query("SELECT * FROM jours_feries ORDER BY date_ferie");
                                while($row = $stmt->fetch()): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($row['nom']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['date_ferie'])) ?></td>
                                    <td><?= $row['recurrent'] ? '<span class="badge badge-success">Oui</span>' : '<span class="badge badge-warning">Non</span>' ?></td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="#" onclick="editFerie(<?= htmlspecialchars(json_encode($row)) ?>)" class="action-btn action-edit" data-tooltip="Modifier ce jour férié"><i class='bx bx-edit-alt'></i></a>
                                            <a href="../controller/parametrage_controller.php?action=delete_ferie&id=<?= $row['id'] ?>" onclick="return confirm('Confirmer la suppression ?')" class="action-btn action-delete" data-tooltip="Supprimer ce jour férié"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                     <!-- Modal Ferie -->
                     <div id="modal-ferie" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 id="modal-ferie-title" class="modal-title">
                                    <span style="background: #10B981; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                        <i class='bx bx-calendar-star'></i>
                                    </span>
                                    Ajouter un jour férié
                                </h3>
                                <button class="modal-close" onclick="closeModal('modal-ferie')"><i class='bx bx-x'></i></button>
                            </div>
                            <div class="modal-body">
                                <form action="../controller/parametrage_controller.php" method="post">
                                    <input type="hidden" name="action" value="save_ferie">
                                    <input type="hidden" name="id" id="ferie-id">
                                    
                                    <div class="form-group">
                                        <label class="form-label">Nom</label>
                                        <input type="text" name="nom" id="ferie-nom" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Date</label>
                                        <input type="date" name="date_ferie" id="ferie-date" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="checkbox-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                            <input type="checkbox" name="recurrent" id="ferie-recurrent" value="1">
                                            <span style="font-size: 0.9rem; color: var(--text-dark);">Récurrent chaque année</span>
                                        </label>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-ferie')">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Valider</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>




                <!-- 5. Départements (NEW) -->
                <?php if ($activeTab == 'departements'): ?>
                    <div class="list-header">
                        <h3>Liste des départements</h3>
                        <button class="btn btn-primary" onclick="openModal('modal-dept')" data-tooltip="Ajouter un nouveau département"><i class='bx bx-plus'></i> Ajouter</button>
                    </div>
                    
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nom du Département</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stmt = $connexion->query("SELECT * FROM departements ORDER BY nom ASC");
                                while($row = $stmt->fetch()): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($row['nom']) ?></td>
                                    <td style="color: var(--text-medium);"><?= htmlspecialchars($row['description']) ?></td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="#" onclick="editDept(<?= htmlspecialchars(json_encode($row)) ?>)" class="action-btn action-edit" data-tooltip="Modifier ce département"><i class='bx bx-edit-alt'></i></a>
                                            <a href="../controller/parametrage_controller.php?action=delete_departement&id=<?= $row['id'] ?>" onclick="return confirm('Confirmer la suppression ?')" class="action-btn action-delete" data-tooltip="Supprimer ce département"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                     <!-- Modal Departement -->
                     <div id="modal-dept" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 id="modal-dept-title" class="modal-title">
                                    <span style="background: #8B5CF6; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                        <i class='bx bx-building-house'></i>
                                    </span>
                                    Ajouter un département
                                </h3>
                                <button class="modal-close" onclick="closeModal('modal-dept')"><i class='bx bx-x'></i></button>
                            </div>
                            <div class="modal-body">
                                <form action="../controller/parametrage_controller.php" method="post">
                                    <input type="hidden" name="action" value="save_departement">
                                    <input type="hidden" name="id" id="dept-id">
                                    
                                    <div class="form-group">
                                        <label class="form-label">Nom du département</label>
                                        <input type="text" name="nom" id="dept-nom" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" id="dept-description" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-dept')">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Valider</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 6. Types d'Evénements (NEW) -->
                <?php if ($activeTab == 'events'): ?>
                    <div class="list-header">
                        <h3>Types d'événements</h3>
                        <button class="btn btn-primary" onclick="openModal('modal-event')" data-tooltip="Ajouter un nouveau type d'événement"><i class='bx bx-plus'></i> Ajouter</button>
                    </div>
                    
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Couleur</th>
                                    <th>Icône</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stmt = $connexion->query("SELECT * FROM event_types ORDER BY nom ASC");
                                while($row = $stmt->fetch()): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($row['nom']) ?></td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <span style="width:20px; height:20px; border-radius:4px; background-color:<?= htmlspecialchars($row['couleur']) ?>; display:inline-block; border:1px solid rgba(0,0,0,0.1);"></span>
                                            <span style="font-size:12px; color:var(--text-medium);"><?= htmlspecialchars($row['couleur']) ?></span>
                                        </div>
                                    </td>
                                    <td><i class='bx <?= htmlspecialchars($row['icon']) ?>' style="font-size:20px; color:var(--text-dark);"></i></td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="#" onclick="editEventType(<?= htmlspecialchars(json_encode($row)) ?>)" class="action-btn action-edit" data-tooltip="Modifier ce type"><i class='bx bx-edit-alt'></i></a>
                                            <a href="../controller/parametrage_controller.php?action=delete_event_type&id=<?= $row['id'] ?>" onclick="return confirm('Confirmer la suppression ?')" class="action-btn action-delete" data-tooltip="Supprimer ce type"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                     <!-- Modal Event Type -->
                     <div id="modal-event" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 id="modal-event-title" class="modal-title">
                                    <span style="background: #F59E0B; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                        <i class='bx bx-calendar-event'></i>
                                    </span>
                                    Ajouter un type d'événement
                                </h3>
                                <button class="modal-close" onclick="closeModal('modal-event')"><i class='bx bx-x'></i></button>
                            </div>
                            <div class="modal-body">
                                <form action="../controller/parametrage_controller.php" method="post">
                                    <input type="hidden" name="action" value="save_event_type">
                                    <input type="hidden" name="id" id="event-id">
                                    
                                    <div class="form-group">
                                        <label class="form-label">Nom</label>
                                        <input type="text" name="nom" id="event-nom" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Couleur</label>
                                        <div style="display:flex; gap:10px; align-items:center;">
                                            <input type="color" name="couleur" id="event-couleur" class="form-control" style="width:60px; padding:2px;" value="#E63946">
                                            <span style="font-size:12px; color:var(--text-medium);">Recommandé: Rouge (#E63946), Bleu (#3B82F6), Vert (#10B981)</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Icône Boxicons (ex: bx-star)</label>
                                        <input type="text" name="icon" id="event-icon" class="form-control" placeholder="bx-calendar" value="bx-calendar-event">
                                        <div style="margin-top:5px; font-size:12px;"><a href="https://boxicons.com/" target="_blank" style="color:var(--red-primary);">Voir la liste des icônes</a></div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-event')">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Valider</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<style>
    .home-content { padding: 24px; }
    
    .settings-container {
        max-width: 100%;
        margin: 0 auto;
    }

    .header-section { margin-bottom: 24px; }
    
    .page-title-group {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .page-title-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--red-primary), var(--red-dark));
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 10px rgba(230, 57, 70, 0.2);
    }
    
    .page-title {
        color: var(--text-dark);
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .page-subtitle {
        color: var(--text-medium);
        margin: 4px 0 0 0;
        font-size: 0.9rem;
    }

    .tabs-wrapper {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--border-light);
        overflow: hidden;
        display: flex;
        flex-direction: row;
        align-items: stretch;
        min-height: 600px;
    }

    .tabs-nav {
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border-light);
        border-bottom: none;
        background: var(--bg-soft);
        padding: 16px 0;
        width: 260px;
        flex-shrink: 0;
    }

    .tab-link {
        padding: 16px 24px;
        text-decoration: none;
        color: var(--text-medium);
        border-left: 3px solid transparent;
        border-bottom: none;
        font-weight: 600;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        white-space: nowrap;
    }

    .tab-link:hover {
        color: var(--red-primary);
        background: rgba(230, 57, 70, 0.05);
    }

    .tab-link.active {
        color: var(--red-primary);
        border-left-color: var(--red-primary);
        background: white;
        box-shadow: 2px 0 10px rgba(0,0,0,0.02);
    }

    .tab-content { 
        padding: 32px;
        flex: 1;
        width: 0; /* Fix flex overflow issues */
    }

    /* Form Styles */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }

    .form-group { margin-bottom: 0; }
    
    .form-group.full-width { grid-column: 1 / -1; }
    
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-medium);
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    textarea.form-control { resize: vertical; }

    .divider {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-dark);
        border-bottom: 2px solid var(--bg-light);
        padding-bottom: 8px;
        margin-top: 24px;
        margin-bottom: 16px;
    }

    .file-upload-wrapper {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .file-preview-link {
        font-size: 13px;
        color: var(--red-primary);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .file-preview-link:hover { text-decoration: underline; }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
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
        box-shadow: 0 6px 8px rgba(230, 57, 70, 0.3); 
    }

    .btn-secondary {
        background-color: white;
        color: var(--text-medium);
        border: 1px solid var(--border-medium);
    }

    .btn-secondary:hover {
        background-color: var(--bg-light);
        color: var(--text-dark);
    }

    /* List & Table Styles */
    .list-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .list-header h3 {
        margin: 0;
        font-size: 1.2rem;
        color: var(--text-dark);
        font-weight: 700;
    }

    .data-table-wrapper {
        background: white;
        border-radius: 12px;
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
        border-bottom: 1px solid var(--border-light);
    }

    .data-table td {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-light);
        vertical-align: middle;
        font-size: 14px;
    }

    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background-color: var(--bg-soft); }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-success { background-color: #D1FAE5; color: #065F46; }
    .badge-warning { background-color: #FEF3C7; color: #92400E; }

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
    
    .action-edit:hover { background: #D97706; color: white; }
    .action-delete:hover { background: #EF4444; color: white; }

    /* Checkbox list style */
    .weekend-days-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 10px;
    }

    .checkbox-label {
        font-size: 14px;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;
    }

    .checkbox-label input { display: none; }

    .custom-checkbox {
        width: 18px;
        height: 18px;
        border: 2px solid var(--border-medium);
        border-radius: 4px;
        display: inline-block;
        position: relative;
        transition: all 0.2s;
    }

    .checkbox-label input:checked + .custom-checkbox {
        background-color: var(--red-primary);
        border-color: var(--red-primary);
    }

    .checkbox-label input:checked + .custom-checkbox::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 6px;
        width: 4px;
        height: 8px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    /* Modal Styles */
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
        margin: 0;
    }
    
    .modal-close {
        background: transparent;
        border: none;
        font-size: 24px;
        line-height: 1;
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
    
    .modal-close:hover {
        background: rgba(0,0,0,0.05);
        color: var(--text-dark);
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
    
    .alert {
        padding: 16px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        box-shadow: var(--shadow-soft);
    }
    
    .alert-success { background-color: #D1FAE5; color: #065F46; }
    .alert-error { background-color: #FEE2E2; color: #991B1B; }

    /* Responsive */
    @media (max-width: 900px) {
        .tabs-wrapper {
            flex-direction: column;
            min-height: auto;
        }

        .tabs-nav {
            width: 100%;
            flex-direction: row;
            border-right: none;
            border-bottom: 1px solid var(--border-light);
            overflow-x: auto;
            padding: 0;
            white-space: nowrap;
        }

        .tab-link {
            border-left: none;
            border-bottom: 3px solid transparent;
            padding: 16px;
        }

        .tab-link.active {
            border-left-color: transparent;
            border-bottom-color: var(--red-primary);
        }

        .form-grid { grid-template-columns: 1fr; }
        .main-layout { grid-template-columns: 1fr; }
    }
</style>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = "block";
    }
    
    function closeModal(id) {
        document.getElementById(id).style.display = "none";
        // Reset forms inside modal if needed
        const form = document.querySelector('#' + id + ' form');
        if(form) form.reset();
        
        // Reset hiding IDs
        const hiddenId = document.querySelector('#' + id + ' input[type="hidden"][name="id"]');
        if(hiddenId) hiddenId.value = '';
    }
    
    function editConge(data) {
        document.getElementById('conge-id').value = data.id;
        document.getElementById('conge-nom').value = data.nom;
        document.getElementById('conge-duree').value = data.duree || 0;
        document.getElementById('conge-description').value = data.description;
        document.getElementById('modal-conge-title').innerHTML = `
            <span style="background: #D97706; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                <i class='bx bx-edit'></i>
            </span>
            Modifier type de congé`;
        openModal('modal-conge');
    }

    function editPoste(data) {
        document.getElementById('poste_id').value = data.id;
        document.getElementById('poste_nom').value = data.titre;
        document.getElementById('poste_desc').value = data.description;
        document.getElementById('poste_dept').value = data.id_departement || '';
        document.getElementById('modal-poste-title').innerHTML = `
            <span style="background: #D97706; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                <i class='bx bx-edit'></i>
            </span>
            Modifier le poste`;
        openModal('modal-poste');
    }
    
    function editFerie(data) {
        document.getElementById('ferie-id').value = data.id;
        document.getElementById('ferie-nom').value = data.nom;
        document.getElementById('ferie-date').value = data.date_ferie;
        document.getElementById('ferie-recurrent').checked = data.recurrent == 1;
        document.getElementById('modal-ferie-title').innerHTML = `
            <span style="background: #D97706; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                <i class='bx bx-edit'></i>
            </span>
            Modifier jour férié`;
        openModal('modal-ferie');
    }
    


    function editDept(data) {
        document.getElementById('dept-id').value = data.id;
        document.getElementById('dept-nom').value = data.nom;
        document.getElementById('dept-description').value = data.description;
        document.getElementById('modal-dept-title').innerHTML = `
            <span style="background: #D97706; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                <i class='bx bx-edit'></i>
            </span>
            Modifier département`;
        openModal('modal-dept');
    }

    function editEventType(data) {
        document.getElementById('event-id').value = data.id;
        document.getElementById('event-nom').value = data.nom;
        document.getElementById('event-couleur').value = data.couleur;
        document.getElementById('event-icon').value = data.icon;
        document.getElementById('modal-event-title').innerHTML = `
            <span style="background: #D97706; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                <i class='bx bx-edit'></i>
            </span>
            Modifier type d'événement`;
        openModal('modal-event');
    }
    
    // Close modal if clicked outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }
</script>

<?php include 'pied.php'; ?>
