<?php
include 'entete.php';

// Récupérer les filtres
$filtres = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['filtrer'])) {
    if (!empty($_GET['date_debut']) && !empty($_GET['date_fin'])) {
        $filtres['date_debut'] = $_GET['date_debut'];
        $filtres['date_fin'] = $_GET['date_fin'];
    }
    
    if (!empty($_GET['id_client'])) {
        $filtres['id_client'] = $_GET['id_client'];
    }
    
    if (!empty($_GET['statut_paiement'])) {
        $filtres['statut_paiement'] = $_GET['statut_paiement'];
    }
}

// Fonction pour récupérer les ventes
function getVentes($filtres = []) {
    global $connexion;
    
    $sql = "SELECT v.*, c.nom, c.prenom, a.nom_article, 
                  f.numero_facture,
                  COALESCE(p.montant_total, v.prix) as montant_total, 
                  COALESCE(p.montant_paye, 0) as montant_paye, 
                  COALESCE(p.reste_a_payer, v.prix) as reste_a_payer,
                  COALESCE(p.statut, 'en_attente') as statut_paiement
            FROM vente v
            JOIN client c ON v.id_client = c.id
            JOIN article a ON v.id_article = a.id
            LEFT JOIN facture f ON f.id_vente = v.id
            LEFT JOIN paiement p ON p.id_vente = v.id
            WHERE v.etat = '1' and v.generer_facture = 1";
    
    $params = [];
    
    // Appliquer les filtres
    if (!empty($filtres['date_debut']) && !empty($filtres['date_fin'])) {
        $sql .= " AND v.date_vente BETWEEN ? AND ?";
        $params[] = $filtres['date_debut'];
        $params[] = $filtres['date_fin'];
    }
    
    if (!empty($filtres['id_client'])) {
        $sql .= " AND v.id_client = ?";
        $params[] = $filtres['id_client'];
    }
    
    if (!empty($filtres['statut_paiement'])) {
        $sql .= " AND COALESCE(p.statut, 'en_attente') = ?";
        $params[] = $filtres['statut_paiement'];
    }
    
    $sql .= " ORDER BY v.date_vente DESC";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les statistiques
function getStatistiques() {
    global $connexion;
    
    $stats = [
        'total_ventes' => 0,
        'total_encaisse' => 0,
        'total_reste' => 0,
        'nb_factures' => 0,
        'nb_factures_payees' => 0,
        'nb_factures_impayees' => 0
    ];
    
    // Total des ventes et montants
    $sql = "SELECT COUNT(v.id) as nb_factures,
                  SUM(COALESCE(p.montant_total, v.prix)) as total_ventes,
                  SUM(COALESCE(p.montant_paye, 0)) as total_encaisse,
                  SUM(COALESCE(p.reste_a_payer, v.prix)) as total_reste,
                  SUM(CASE WHEN COALESCE(p.statut, 'en_attente') = 'complet' THEN 1 ELSE 0 END) as nb_factures_payees,
                  SUM(CASE WHEN COALESCE(p.statut, 'en_attente') != 'complet' THEN 1 ELSE 0 END) as nb_factures_impayees
            FROM vente v
            LEFT JOIN paiement p ON p.id_vente = v.id
            WHERE v.etat = '1'   and v.generer_facture = 1";
    
    $req = $connexion->query($sql);
    $resultats = $req->fetch(PDO::FETCH_ASSOC);
    
    if ($resultats) {
        $stats['nb_factures'] = $resultats['nb_factures'] ?? 0;
        $stats['total_ventes'] = $resultats['total_ventes'] ?? 0;
        $stats['total_encaisse'] = $resultats['total_encaisse'] ?? 0;
        $stats['total_reste'] = $resultats['total_reste'] ?? 0;
        $stats['nb_factures_payees'] = $resultats['nb_factures_payees'] ?? 0;
        $stats['nb_factures_impayees'] = $resultats['nb_factures_impayees'] ?? 0;
    }
    
    return $stats;
}

// Récupérer les ventes et les statistiques
$ventes = getVentes($filtres);
$stats = getStatistiques();
?>

<div class="home-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #0a2558;">Gestion des Factures VISION KA</h2>
        
        <div>
            <a href="vente.php" class="btn-action" style="background-color: #2a9d8f; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; margin-right: 10px;">
                <i class='bx bx-plus'></i> Nouvelle Vente
            </a>
            
            <button id="toggle-filter" class="btn-action" style="background-color: #0a2558; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                <i class='bx bx-filter-alt'></i> Filtres
            </button>
        </div>
    </div>
    
    <!-- Filtres (masqués par défaut) -->
    <div id="filter-section" style="margin-bottom: 20px; padding: 15px; background-color: #f0f7ff; border-radius: 5px; display: <?= !empty($filtres) ? 'block' : 'none' ?>;">
        <form method="GET" action="">
            <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
                <div style="flex: 1; min-width: 200px;">
                    <label for="date_debut" style="display: block; margin-bottom: 5px;">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?= $filtres['date_debut'] ?? '' ?>">
                </div>
                
                <div style="flex: 1; min-width: 200px;">
                    <label for="date_fin" style="display: block; margin-bottom: 5px;">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?= $filtres['date_fin'] ?? '' ?>">
                </div>
                
                <div style="flex: 1; min-width: 200px;">
                    <label for="id_client" style="display: block; margin-bottom: 5px;">Client</label>
                    <select name="id_client" id="id_client" class="form-control">
                        <option value="">Tous les clients</option>
                        <?php
                        $clients = getClient();
                        if (!empty($clients) && is_array($clients)) {
                            foreach ($clients as $client) {
                                $selected = (isset($filtres['id_client']) && $filtres['id_client'] == $client['id']) ? 'selected' : '';
                                echo "<option value=\"{$client['id']}\" $selected>{$client['nom']} {$client['prenom']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div style="flex: 1; min-width: 200px;">
                    <label for="statut_paiement" style="display: block; margin-bottom: 5px;">Statut paiement</label>
                    <select name="statut_paiement" id="statut_paiement" class="form-control">
                        <option value="">Tous les statuts</option>
                        <option value="complet" <?= (isset($filtres['statut_paiement']) && $filtres['statut_paiement'] == 'complet') ? 'selected' : '' ?>>Payée</option>
                        <option value="partiel" <?= (isset($filtres['statut_paiement']) && $filtres['statut_paiement'] == 'partiel') ? 'selected' : '' ?>>Partiellement payée</option>
                        <option value="en_attente" <?= (isset($filtres['statut_paiement']) && $filtres['statut_paiement'] == 'en_attente') ? 'selected' : '' ?>>En attente</option>
                    </select>
                </div>
                
                <div style="flex: 0.5; min-width: 100px;">
                    <button type="submit" name="filtrer" value="1" class="btn-search" style="width: 100%; background-color: #0a2558; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                        <i class='bx bx-search'></i> Rechercher
                    </button>
                </div>
                
                <div style="flex: 0.5; min-width: 100px;">
                    <a href="dashboard.php" class="btn-reset" style="display: block; text-align: center; background-color: #6c757d; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px;">
                        <i class='bx bx-reset'></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Statistiques -->
    <div class="stats-section" style="margin-bottom: 30px;">
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            <div class="stat-card" style="flex: 1; min-width: 200px; background: linear-gradient(135deg, #0a2558, #1e429f); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; margin-bottom: 10px;">Total des Factures</div>
                <div style="font-size: 24px; font-weight: bold;"><?= $stats['nb_factures'] ?></div>
                <div style="font-size: 14px; margin-top: 10px;">Montant: <?= number_format($stats['total_ventes'], 2, ',', ' ') ?> DH</div>
            </div>
            
            <div class="stat-card" style="flex: 1; min-width: 200px; background: linear-gradient(135deg, #2a9d8f, #3caf9f); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; margin-bottom: 10px;">Montant Encaissé</div>
                <div style="font-size: 24px; font-weight: bold;"><?= number_format($stats['total_encaisse'], 2, ',', ' ') ?> DH</div>
                <div style="font-size: 14px; margin-top: 10px;">Factures payées: <?= $stats['nb_factures_payees'] ?></div>
            </div>
            
            <div class="stat-card" style="flex: 1; min-width: 200px; background: linear-gradient(135deg, #e76f51, #f4a261); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; margin-bottom: 10px;">Reste à Encaisser</div>
                <div style="font-size: 24px; font-weight: bold;"><?= number_format($stats['total_reste'], 2, ',', ' ') ?> DH</div>
                <div style="font-size: 14px; margin-top: 10px;">Factures impayées: <?= $stats['nb_factures_impayees'] ?></div>
            </div>
        </div>
    </div>
    
    <!-- Tableau des ventes -->
    <div class="table-section" style="background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: #0a2558; font-size: 1.2em;">Liste des Factures</h3>
            
            <div>
                <button id="print-all" class="btn-action" style="background-color: #457b9d; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                    <i class='bx bx-printer'></i> Imprimer Sélection
                </button>
                
                <a href="rapport_factures.php" class="btn-action" style="background-color: #2a9d8f; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; margin-left: 10px;">
                    <i class='bx bx-file-blank'></i> Exporter Rapport
                </a>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f2f2f2; text-align: left;">
                        <th style="padding: 12px 15px; border-bottom: 1px solid #ddd; width: 30px;">
                            <input type="checkbox" id="select-all" style="cursor: pointer;">
                        </th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #ddd;">N° Facture</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #ddd;">Date</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #ddd;">Client</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #ddd;">Article</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: right;">Montant</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: right;">Payé</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: right;">Reste</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #ddd;">Statut</th>
                        <th style="padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ventes)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 20px; color: #6c757d;">
                                Aucune facture trouvée
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ventes as $index => $vente): ?>
                            <tr class="<?= $index % 2 === 0 ? '' : 'tr-alt' ?>" style="<?= $index % 2 === 0 ? '' : 'background-color: #f9f9f9;' ?>">
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee;">
                                    <input type="checkbox" class="facture-checkbox" data-id="<?= $vente['id'] ?>" style="cursor: pointer;">
                                </td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee;">
                                    <?php if (!empty($vente['numero_facture'])): ?>
                                        <?= $vente['numero_facture'] ?>
                                    <?php else: ?>
                                        <?= sprintf('VISION-KA-%04d', $vente['id']) ?>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee;">
                                    <?= date('d/m/Y', strtotime($vente['date_vente'])) ?>
                                </td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee;">
                                    <?= $vente['nom'] . ' ' . $vente['prenom'] ?>
                                </td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee;">
                                    <?= $vente['nom_article'] ?>
                                </td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;">
                                    <?= number_format($vente['montant_total'], 2, ',', ' ') ?> DH
                                </td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee; text-align: right;">
                                    <?= number_format($vente['montant_paye'], 2, ',', ' ') ?> DH
                                </td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee; text-align: right; <?= $vente['reste_a_payer'] > 0 ? 'color: #e76f51; font-weight: bold;' : 'color: #2a9d8f;' ?>">
                                    <?= number_format($vente['reste_a_payer'], 2, ',', ' ') ?> DH
                                </td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee;">
                                    <?php
                                    $statut_class = '';
                                    $statut_text = '';
                                    
                                    switch($vente['statut_paiement']) {
                                        case 'complet':
                                            $statut_class = 'bg-success';
                                            $statut_text = 'Payée';
                                            break;
                                        case 'partiel':
                                            $statut_class = 'bg-warning';
                                            $statut_text = 'Partielle';
                                            break;
                                        case 'en_attente':
                                        default:
                                            $statut_class = 'bg-danger';
                                            $statut_text = 'En attente';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $statut_class ?>" style="padding: 5px 8px; border-radius: 4px; font-size: 12px; color: white;">
                                        <?= $statut_text ?>
                                    </span>
                                </td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee; text-align: center;">
                                   
                                    <a href="facture_vision_ka.php?id=<?= $vente['id'] ?>" class="btn-icon" title="Imprimer facture duplicata">
    <i class='bx bx-printer'></i>
</a>
 <a href="facture.php?id=<?= $vente['id'] ?>" class="btn-icon" title="Imprimer facture">
    <i class='bx bx-printer'></i>
</a>
                                    <a href="detail_vente.php?id=<?= $vente['id'] ?>" class="btn-icon" title="Voir détails" style="color: #2a9d8f; margin: 0 3px;">
                                        <i class='bx bx-info-circle'></i>
                                    </a>
                                    
                                    <?php if ($vente['statut_paiement'] !== 'complet'): ?>
                                    <a href="ajouter_paiement.php?id_vente=<?= $vente['id'] ?>" class="btn-icon" title="Ajouter paiement" style="color: #e76f51; margin: 0 3px;">
                                        <i class='bx bx-money'></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="modifier_vente.php?id=<?= $vente['id'] ?>" class="btn-icon" title="Modifier" style="color: #457b9d; margin: 0 3px;">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.bg-success {
    background-color: #2a9d8f;
}
.bg-warning {
    background-color: #f4a261;
}
.bg-danger {
    background-color: #e76f51;
}
.btn-icon {
    display: inline-block;
    padding: 5px;
    border-radius: 4px;
    cursor: pointer;
}
.btn-icon:hover {
    background-color: #f2f2f2;
}
</style>

<script>
    // Toggle des filtres
    document.getElementById('toggle-filter').addEventListener('click', function() {
        const filterSection = document.getElementById('filter-section');
        filterSection.style.display = filterSection.style.display === 'none' ? 'block' : 'none';
    });
    
    // Sélectionner/désélectionner toutes les factures
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.facture-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Imprimer les factures sélectionnées
    document.getElementById('print-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.facture-checkbox:checked');
        
        if (checkboxes.length === 0) {
            alert('Veuillez sélectionner au moins une facture à imprimer');
            return;
        }
        
        // Collecter les IDs des factures sélectionnées
        const ids = Array.from(checkboxes).map(checkbox => checkbox.dataset.id);
        
        // Ouvrir une nouvelle fenêtre pour chaque facture sélectionnée
        ids.forEach(id => {
            window.open('facture_vision_ka.php?id=' + id, '_blank');
        });
    });
</script>

<?php
include 'pied.php';
?>
