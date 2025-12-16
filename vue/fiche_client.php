<?php
include 'entete.php';

if (empty($_GET['id'])) {
    header('Location: client.php');
    exit();
}

$client_id = $_GET['id'];

// Récupérer les informations du client directement
$sql_client = "SELECT * FROM client WHERE id = ?";
$req_client = $connexion->prepare($sql_client);
$req_client->execute(array($client_id));
$client = $req_client->fetch();

if (!$client) {
    $_SESSION['message']['text'] = "Client non trouvé";
    $_SESSION['message']['type'] = "danger";
    header('Location: client.php');
    exit();
}

// Récupérer les prescriptions directement
$sql_prescriptions = "SELECT * FROM prescription WHERE id_client = ? ORDER BY date_prescription DESC";
$req_prescriptions = $connexion->prepare($sql_prescriptions);
$req_prescriptions->execute(array($client_id));
$prescriptions = $req_prescriptions->fetchAll();

// Récupérer les ventes avec leurs détails
$sql_ventes = "SELECT v.id, v.date_vente, v.prix, v.quantite, v.commentaires, v.generer_facture, 
                      v.id_client, v.id_article, v.date_livraison, 
                      a.nom_article, a.marque, a.modele, a.id_categorie,
                      c.libelle_categorie,
                      p.id as id_paiement, p.montant_total, p.montant_paye, p.reste_a_payer, 
                      p.mode_paiement, p.statut as statut_paiement,
                      (SELECT COUNT(*) FROM ligne_vente WHERE id_vente = v.id) as nb_lignes
               FROM vente v 
               JOIN article a ON v.id_article = a.id 
               JOIN categorie_article c ON a.id_categorie = c.id 
               LEFT JOIN paiement p ON v.id = p.id_vente
               WHERE v.id_client = ? 
               ORDER BY v.date_vente DESC";
$req_ventes = $connexion->prepare($sql_ventes);
$req_ventes->execute(array($client_id));
$ventes = $req_ventes->fetchAll();

// Récupérer les garanties actives du client
$sql_garanties = "SELECT gl.*, lv.id_vente, v.date_vente, a.nom_article, a.marque, a.modele, a.reference 
                  FROM garantie_ligne gl 
                  JOIN ligne_vente lv ON gl.id_ligne_vente = lv.id 
                  JOIN vente v ON lv.id_vente = v.id 
                  JOIN article a ON lv.id_article = a.id 
                  WHERE v.id_client = ? AND gl.statut = 'active'
                  ORDER BY gl.date_fin DESC";
$req_garanties = $connexion->prepare($sql_garanties);
$req_garanties->execute(array($client_id));
$garanties_actives = $req_garanties->fetchAll();

// Fonction pour formater les prix
function formatPrix($prix) {
    return number_format($prix, 0, ',', ' ') . ' DH';
}
?>

<style>
    /* Variables de couleurs basées sur votre fichier existant */
    :root {
        --primary-color: #0a2558; /* Couleur principale de votre thème existant */
        --secondary-color: #2a9d8f; /* Couleur secondaire pour les boutons d'action */
        --text-color: #495057;
        --border-color: #e9ecef;
        --light-bg: #f8f9fa;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }
    
    /* Adaptation à la pleine page */
    .home-content {
        padding: 0;
        width: 100%;
    }
    
    .overview-boxes {
        display: block; /* Changement pour adapter à pleine largeur */
        width: 100%;
        padding: 0;
        margin: 0;
    }
    
    /* Conteneur principal */
    .client-container {
        width: 100%;
        padding: 0;
        margin: 0;
    }
    
    /* Carte d'information client */
    .client-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: var(--shadow);
        margin-bottom: 25px;
        overflow: hidden;
        width: 100%;
    }
    
    .client-header {
        background-color: var(--primary-color);
        color: white;
        padding: 20px;
        position: relative;
    }
    
    .client-header h2 {
        font-size: 22px;
        margin-bottom: 5px;
    }
    
    .client-header p {
        opacity: 0.9;
        font-size: 14px;
    }
    
    .client-actions {
        position: absolute;
        top: 20px;
        right: 20px;
    }
    
    .client-info {
        padding: 20px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
    }
    
    .info-item {
        margin-bottom: 10px;
    }
    
    .info-item label {
        display: block;
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .info-item p {
        font-size: 15px;
        color: var(--text-color);
    }
    
    /* Système d'onglets */
    .tabs {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 20px;
        background-color: white;
        border-radius: 8px 8px 0 0;
        overflow: hidden;
    }
    
    .tab {
        padding: 15px 25px;
        cursor: pointer;
        transition: var(--transition);
        border-bottom: 3px solid transparent;
        font-weight: 500;
        color: var(--text-color);
    }
    
    .tab.active {
        border-bottom: 3px solid var(--primary-color);
        color: var(--primary-color);
        background-color: rgba(10, 37, 88, 0.05);
    }
    
    .tab:hover:not(.active) {
        background-color: #f1f5f9;
    }
    
    .tab i {
        margin-right: 8px;
    }
    
    .tab-content {
        display: none;
        background-color: white;
        border-radius: 0 0 8px 8px;
        padding: 20px;
        box-shadow: var(--shadow);
        margin-bottom: 25px;
    }
    
    .tab-content.active {
        display: block;
    }
    
    /* Section */
    .section {
        margin-bottom: 30px;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--primary-color);
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 8px;
    }
    
    /* Badge */
    .badge {
        background-color: #e9ecef;
        color: #495057;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        margin-left: 8px;
    }
    
    /* Tableaux améliorés */
    .table-responsive {
        overflow-x: auto;
        width: 100%;
    }
    
    .modern-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }
    
    .modern-table th {
        background-color: var(--light-bg);
        font-weight: 500;
        color: var(--primary-color);
    }
    
    .modern-table tr:last-child td {
        border-bottom: none;
    }
    
    .modern-table tr:hover {
        background-color: #f1f5f9;
    }
    
    /* Boutons d'action */
    .btn-action {
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: white;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: #0d3070;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background-color: var(--secondary-color);
    }
    
    .btn-secondary:hover {
        background-color: #3ab4a6;
        transform: translateY(-2px);
    }
    
    .btn-link {
        color: var(--primary-color);
        background: transparent;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 4px;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-link:hover {
        background-color: rgba(10, 37, 88, 0.1);
    }
    
    .btn-group {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    /* Actions cellules */
    .actions-cell {
        display: flex;
        gap: 15px;
    }
    
    .action-icon {
        font-size: 18px;
        color: var(--text-color);
        opacity: 0.7;
        transition: var(--transition);
    }
    
    .action-icon:hover {
        opacity: 1;
        color: var(--primary-color);
    }
    
    /* État vide */
    .empty-state {
        text-align: center;
        padding: 40px 0;
        color: #6c757d;
        background-color: var(--light-bg);
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .empty-state i {
        font-size: 50px;
        color: #ced4da;
        margin-bottom: 15px;
        display: block;
    }
    
    /* Badges de statut */
    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-active {
        background-color: #e3fcef;
        color: #0d6832;
    }
    
    .status-expired {
        background-color: #f8f9fa;
        color: #6c757d;
    }
    
    .status-used {
        background-color: #e7f5ff;
        color: #1864ab;
    }
    
    /* Carte */
    .card {
        background-color: white;
        border-radius: 8px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .card-header {
        padding: 15px 20px;
        background-color: var(--light-bg);
        border-bottom: 1px solid var(--border-color);
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .card-body {
        padding: 20px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .tabs {
            overflow-x: auto;
        }
        
        .tab {
            white-space: nowrap;
            padding: 12px 15px;
        }
        
        .btn-group {
            flex-wrap: wrap;
        }
        
        .client-actions {
            position: relative;
            top: 0;
            right: 0;
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
        }
    }
</style>

<div class="home-content">
    <div class="overview-boxes">
        <div class="client-container">
            <div class="btn-group">
                <a href="client.php" class="btn-link">
                    <i class='bx bx-arrow-back'></i> Retour
                </a>
                <a href="client.php?id=<?php echo $client['id']; ?>" class="btn-action btn-primary">
                    <i class='bx bx-edit-alt'></i> Modifier
                </a>
            </div>
            
            <!-- Carte d'information client -->
            <div class="client-card">
                <div class="client-header">
                    <h2><?php echo $client['nom'] . ' ' . $client['prenom']; ?></h2>
                    <p><i class='bx bx-phone'></i> <?php echo $client['telephone']; ?></p>
                    <div class="client-actions">
                        <a href="vente.php?client_id=<?php echo $client['id']; ?>" class="btn-action btn-secondary" title="Nouvelle vente">
                            <i class='bx bx-cart-add'></i> Nouvelle vente
                        </a>
                    </div>
                </div>
                
                <div class="client-info">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Adresse</label>
                            <p><?php echo $client['adresse']; ?></p>
                        </div>
                        <div class="info-item">
                            <label>Email</label>
                            <p><?php echo !empty($client['email']) ? $client['email'] : '-'; ?></p>
                        </div>
                        <div class="info-item">
                            <label>Date de naissance</label>
                            <p><?php echo !empty($client['date_naissance']) ? date('d/m/Y', strtotime($client['date_naissance'])) : '-'; ?></p>
                        </div>
                        <div class="info-item">
                            <label>Mutuelle</label>
                            <p><?php echo !empty($client['mutuelle']) ? $client['mutuelle'] : '-'; ?></p>
                        </div>
                        <div class="info-item">
                            <label>N° de sécurité sociale</label>
                            <p><?php echo !empty($client['numero_secu']) ? $client['numero_secu'] : '-'; ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($client['commentaires'])): ?>
                    <div class="info-item" style="margin-top: 15px;">
                        <label>Commentaires</label>
                        <p><?php echo nl2br($client['commentaires']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Actions rapides -->
            <div class="btn-group">
                <a href="prescription.php?client_id=<?php echo $client['id']; ?>" class="btn-action btn-primary">
                    <i class='bx bx-plus'></i> Nouvelle prescription
                </a>
                <a href="vente.php?client_id=<?php echo $client['id']; ?>" class="btn-action btn-secondary">
                    <i class='bx bx-cart-add'></i> Nouvelle vente
                </a>
            </div>
            
            <!-- Système d'onglets -->
            <div class="tabs">
                <div class="tab active" data-tab="prescriptions">
                    <i class='bx bx-file-find'></i> Prescriptions
                </div>
                <div class="tab" data-tab="achats">
                    <i class='bx bx-shopping-bag'></i> Achats
                </div>
                <div class="tab" data-tab="garanties">
                    <i class='bx bx-shield-quarter'></i> Garanties
                </div>
            </div>
            
            <!-- Onglet Prescriptions -->
            <div class="tab-content active" id="prescriptions">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class='bx bx-file-find'></i>
                        Prescriptions
                        <span class="badge"><?php echo count($prescriptions); ?></span>
                    </h3>
                </div>
                
                <?php if (!empty($prescriptions)): ?>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Médecin</th>
                                <th>Valide jusqu'à</th>
                                <th>Détails</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prescriptions as $p): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($p['date_prescription'])); ?></td>
                                <td><?php echo !empty($p['medecin']) ? $p['medecin'] : '-'; ?></td>
                                <td><?php echo !empty($p['valide_jusqu_a']) ? date('d/m/Y', strtotime($p['valide_jusqu_a'])) : '-'; ?></td>
                                <td>
                                    <?php
                                    // Récupérer les détails de la prescription
                                    $sql_details = "SELECT * FROM details_prescription WHERE id_prescription = ?";
                                    $req_details = $connexion->prepare($sql_details);
                                    $req_details->execute(array($p['id']));
                                    $details = $req_details->fetchAll();
                                    
                                    if (!empty($details)) {
                                        foreach ($details as $d) {
                                            $oeil = ($d['type'] == 'droite') ? 'OD' : 'OG';
                                            echo "<span style='display:inline-block;margin-right:15px;'><strong>{$oeil}</strong>: ";
                                            echo "S: {$d['sphere']} C: {$d['cylindre']} A: {$d['axe']}°";
                                            if (!empty($d['addition'])) echo " Add: {$d['addition']}";
                                            echo "</span>";
                                        }
                                    } else {
                                        echo "-";
                                    }
                                    ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="prescription.php?id=<?php echo $p['id']; ?>" class="action-icon" title="Modifier">
                                        <i class='bx bx-edit-alt'></i>
                                    </a>
                                    <a href="../model/imprimerPrescription.php?id=<?php echo $p['id']; ?>" target="_blank" class="action-icon" title="Imprimer">
                                        <i class='bx bx-printer'></i>
                                    </a>
                                    <a href="vente.php?prescription_id=<?php echo $p['id']; ?>&client_id=<?php echo $client['id']; ?>" class="action-icon" title="Créer une vente">
                                        <i class='bx bx-cart-add'></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class='bx bx-file'></i>
                    <p>Aucune prescription enregistrée pour ce client.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Onglet Achats -->
            <div class="tab-content" id="achats">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class='bx bx-shopping-bag'></i>
                        Historique des achats
                        <span class="badge"><?php echo count($ventes); ?></span>
                    </h3>
                </div>
                
                <?php if (!empty($ventes)): ?>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Article</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Prix</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ventes as $v): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($v['date_vente'])); ?></td>
                                <td>
                                    <?php echo $v['nom_article']; ?> 
                                    <?php echo !empty($v['marque']) ? '- ' . $v['marque'] : ''; ?> 
                                    <?php echo !empty($v['modele']) ? $v['modele'] : ''; ?>
                                    <?php if (!empty($v['nb_lignes']) && $v['nb_lignes'] > 0): ?>
                                    <span class="badge">
                                        +<?php echo $v['nb_lignes']; ?> article<?php echo $v['nb_lignes'] > 1 ? 's' : ''; ?>
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $v['libelle_categorie']; ?></td>
                                <td><?php echo $v['quantite']; ?></td>
                                <td><?php echo formatPrix($v['prix']); ?></td>
                                <td>
                                    <?php if (!empty($v['statut_paiement'])): ?>
                                    <span class="status-badge <?php echo $v['statut_paiement'] == 'complet' ? 'status-active' : ($v['statut_paiement'] == 'partiel' ? 'status-used' : 'status-expired'); ?>">
                                        <?php echo ucfirst($v['statut_paiement']); ?>
                                    </span>
                                    <?php else: ?>
                                    -
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="recuVente.php?id=<?php echo $v['id']; ?>" class="action-icon" title="Imprimer reçu">
                                        <i class='bx bx-receipt'></i>
                                    </a>
                                    <a href="detail_vente.php?id=<?php echo $v['id']; ?>" class="action-icon" title="Voir détails">
                                        <i class='bx bx-info-circle'></i>
                                    </a>
                                    <?php if ($v['date_livraison'] >= date('Y-m-d')): ?>
                                    <a href="../model/annulerVente.php?id=<?php echo $v['id']; ?>" class="action-icon" title="Annuler la vente" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette vente?');">
                                        <i class='bx bx-x-circle' style="color: #e63946;"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class='bx bx-cart'></i>
                    <p>Aucun achat enregistré pour ce client.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Onglet Garanties -->
            <div class="tab-content" id="garanties">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class='bx bx-shield-quarter'></i>
                        Garanties actives
                        <span class="badge"><?php echo count($garanties_actives); ?></span>
                    </h3>
                </div>
                
                <?php if (!empty($garanties_actives)): ?>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Type</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($garanties_actives as $g): ?>
                            <tr>
                                <td><?php echo $g['nom_article']; ?> <?php echo !empty($g['marque']) ? '- ' . $g['marque'] : ''; ?> <?php echo !empty($g['modele']) ? $g['modele'] : ''; ?></td>
                                <td>
                                    <?php
                                    switch ($g['type_garantie']) {
                                        case 'monture':
                                            echo 'Monture';
                                            break;
                                        case 'verres':
                                            echo 'Verres';
                                            break;
                                        case 'monture_et_verres':
                                            echo 'Monture et verres';
                                            break;
                                        default:
                                            echo 'Autre';
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($g['date_debut'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($g['date_fin'])); ?></td>
                                <td class="actions-cell">
                                    <a href="detail_garantie.php?id=<?php echo $g['id']; ?>" class="action-icon" title="Voir détails">
                                        <i class='bx bx-info-circle'></i>
                                    </a>
                                    <a href="imprimer_garantie.php?id=<?php echo $g['id']; ?>" class="action-icon" title="Imprimer certificat">
                                        <i class='bx bx-printer'></i>
                                    </a>
                                    <a href="../model/utiliserGarantie.php?id=<?php echo $g['id']; ?>" class="action-icon" title="Marquer comme utilisée" onclick="return confirm('Êtes-vous sûr de vouloir marquer cette garantie comme utilisée?');">
                                        <i class='bx bx-check-circle' style="color: #2a9d8f;"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class='bx bx-shield'></i>
                    <p>Aucune garantie active pour ce client.</p>
                </div>
                <?php endif; ?>
                
                <div style="margin-top: 20px;">
                    <a href="garanties.php?client_id=<?php echo $client_id; ?>" class="btn-action btn-primary">
                        <i class='bx bx-list-ul'></i>
                        Voir toutes les garanties
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fonctionnalité des onglets
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab');
                
                // Retirer la classe active de tous les onglets et contenus
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Ajouter la classe active à l'onglet courant et à son contenu
                tab.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    });
</script>

<?php
include 'pied.php';
?>