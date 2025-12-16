<?php
// Activer l'affichage de toutes les erreurs pour le débogage
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

include 'entete.php';

// Vérifier s'il s'agit d'une demande d'export
$export = isset($_GET['export']) && $_GET['export'] === 'excel';

// Récupérer les filtres
$filtres = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['date_debut']) && !empty($_GET['date_fin'])) {
        $filtres['date_debut'] = $_GET['date_debut'];
        $filtres['date_fin'] = $_GET['date_fin'];
    } else {
        // Par défaut, prendre le mois en cours
        $filtres['date_debut'] = date('Y-m-01'); // Premier jour du mois
        $filtres['date_fin'] = date('Y-m-t'); // Dernier jour du mois
    }
    
    if (!empty($_GET['id_client'])) {
        $filtres['id_client'] = $_GET['id_client'];
    }
}

// Fonction pour récupérer les factures
function getFactures($filtres = []) {
    global $connexion;
    
    $sql = "SELECT v.id, v.date_vente, v.quantite, v.prix,
                  c.id as id_client, c.nom, c.prenom
            FROM vente v
            JOIN client c ON v.id_client = c.id
            WHERE v.etat = '1'";
    
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
    
    $sql .= " ORDER BY v.date_vente DESC";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les factures
$factures = getFactures($filtres);
$total_factures = count($factures);

// Calculer les totaux
$total_montant = 0;

foreach ($factures as $facture) {
    $total_montant += $facture['prix'];
}

// Données pour les graphiques
function getFacturesParJour($factures) {
    $result = [];
    $data = [];
    
    // Regrouper les factures par jour
    foreach ($factures as $facture) {
        $date = date('Y-m-d', strtotime($facture['date_vente']));
        
        if (!isset($data[$date])) {
            $data[$date] = [
                'count' => 0,
                'amount' => 0
            ];
        }
        
        $data[$date]['count']++;
        $data[$date]['amount'] += $facture['prix'];
    }
    
    // Convertir en tableau pour le graphique
    foreach ($data as $date => $values) {
        $result[] = [
            'date' => date('d/m/Y', strtotime($date)),
            'count' => $values['count'],
            'amount' => $values['amount']
        ];
    }
    
    // Trier par date
    usort($result, function($a, $b) {
        return strtotime(str_replace('/', '-', $a['date'])) - strtotime(str_replace('/', '-', $b['date']));
    });
    
    return $result;
}

// Obtenir les données pour les graphiques
$factures_par_jour = getFacturesParJour($factures);

// Exporter vers Excel si demandé
if ($export) {
    // Définir l'en-tête pour le téléchargement
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="rapport_factures_vision_ka_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    // Début du document Excel
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Rapport Factures VISION KA</title>
        <style>
            table {border-collapse: collapse;}
            th, td {border: 1px solid black; padding: 5px;}
            th {background-color: #f2f2f2; font-weight: bold;}
            .total {font-weight: bold; background-color: #e6e6e6;}
        </style>
    </head>
    <body>';
    
    // Titre du rapport
    echo '<h1>Rapport des Factures VISION KA</h1>';
    echo '<p>Période: ' . date('d/m/Y', strtotime($filtres['date_debut'])) . ' au ' . date('d/m/Y', strtotime($filtres['date_fin'])) . '</p>';
    
    // Résumé
    echo '<h2>Résumé</h2>';
    echo '<table>
        <tr>
            <th>Total Factures</th>
            <th>Montant Total</th>
        </tr>
        <tr>
            <td>' . $total_factures . '</td>
            <td>' . number_format($total_montant, 2, ',', ' ') . ' DH</td>
        </tr>
    </table>';
    
    // Liste détaillée des factures
    echo '<h2>Liste des Factures</h2>';
    echo '<table>
        <tr>
            <th>N° Facture</th>
            <th>Date</th>
            <th>Client</th>
            <th>Montant</th>
        </tr>';
    
    foreach ($factures as $facture) {
        echo '<tr>
            <td>VISION-KA-' . sprintf('%04d', $facture['id']) . '</td>
            <td>' . date('d/m/Y', strtotime($facture['date_vente'])) . '</td>
            <td>' . $facture['nom'] . ' ' . $facture['prenom'] . '</td>
            <td>' . number_format($facture['prix'], 2, ',', ' ') . ' DH</td>
        </tr>';
    }
    
    echo '</table>';
    
    // Pied de page
    echo '<p>Rapport généré le ' . date('d/m/Y à H:i') . ' - VISION KA</p>';
    
    echo '</body></html>';
    exit;
}
?>

<div class="home-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #0a2558;">Rapport des Factures VISION KA</h2>
        
        <div>
            <a href="rapport_factures.php?export=excel<?= !empty($filtres) ? '&' . http_build_query($filtres) : '' ?>" class="btn-action" style="background-color: #2a9d8f; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; margin-right: 10px;">
                <i class='bx bx-download'></i> Exporter Excel
            </a>
            
            <a href="vente.php" class="btn-action" style="background-color: #6c757d; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px;">
                <i class='bx bx-arrow-back'></i> Retour
            </a>
        </div>
    </div>
    
    <!-- Filtres -->
    <div id="filter-section" style="margin-bottom: 20px; padding: 15px; background-color: #f0f7ff; border-radius: 5px;">
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
                
                <div style="flex: 0.5; min-width: 100px;">
                    <button type="submit" class="btn-search" style="width: 100%; background-color: #0a2558; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                        <i class='bx bx-search'></i> Générer
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Résumé du rapport -->
    <div style="margin-bottom: 30px; background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; color: #0a2558; font-size: 1.2em; margin-bottom: 15px;">Résumé du Rapport</h3>
        
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px;">
            <div style="flex: 1; min-width: 200px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; text-align: center;">
                <div style="font-size: 14px; color: #6c757d; margin-bottom: 5px;">Total Factures</div>
                <div style="font-size: 24px; font-weight: bold; color: #0a2558;"><?= $total_factures ?></div>
            </div>
            
            <div style="flex: 1; min-width: 200px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; text-align: center;">
                <div style="font-size: 14px; color: #6c757d; margin-bottom: 5px;">Montant Total</div>
                <div style="font-size: 24px; font-weight: bold; color: #0a2558;"><?= number_format($total_montant, 2, ',', ' ') ?> DH</div>
            </div>
        </div>
    </div>
    
    <!-- Graphique d'évolution des factures -->
    <div style="margin-bottom: 30px; background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; color: #0a2558; font-size: 1.2em; margin-bottom: 15px;">Évolution des Factures</h3>
        <div id="chart-evolution" style="height: 300px;"></div>
    </div>
    
    <!-- Liste détaillée des factures -->
    <div style="background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; color: #0a2558; font-size: 1.2em; margin-bottom: 15px;">Liste des Factures</h3>
        
        <?php if (empty($factures)): ?>
            <p style="text-align: center; padding: 20px; color: #6c757d;">Aucune facture trouvée pour la période sélectionnée</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f2f2f2; text-align: left;">
                            <th style="padding: 12px 15px; border-bottom: 1px solid #ddd;">N° Facture</th>
                            <th style="padding: 12px 15px; border-bottom: 1px solid #ddd;">Date</th>
                            <th style="padding: 12px 15px; border-bottom: 1px solid #ddd;">Client</th>
                            <th style="padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: right;">Montant</th>
                            <th style="padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($factures as $index => $facture): ?>
                            <tr class="<?= $index % 2 === 0 ? '' : 'tr-alt' ?>" style="<?= $index % 2 === 0 ? '' : 'background-color: #f9f9f9;' ?>; border-bottom: 1px solid #eee;">
                                <td style="padding: 12px 15px;">
                                    <?= sprintf('VISION-KA-%04d', $facture['id']) ?>
                                </td>
                                <td style="padding: 12px 15px;">
                                    <?= date('d/m/Y', strtotime($facture['date_vente'])) ?>
                                </td>
                                <td style="padding: 12px 15px;">
                                    <?= $facture['nom'] . ' ' . $facture['prenom'] ?>
                                </td>
                                <td style="padding: 12px 15px; text-align: right; font-weight: bold;">
                                    <?= number_format($facture['prix'], 2, ',', ' ') ?> DH
                                </td>
                                <td style="padding: 12px 15px; text-align: center;">
                                    <a href="facture_minimale.php?id=<?= $facture['id'] ?>" class="btn-icon" title="Imprimer facture" style="color: #0a2558; margin: 0 3px;">
                                        <i class='bx bx-printer'></i>
                                    </a>
                                    
                                    <a href="detail_vente.php?id=<?= $facture['id'] ?>" class="btn-icon" title="Voir détails" style="color: #2a9d8f; margin: 0 3px;">
                                        <i class='bx bx-info-circle'></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f2f2f2; font-weight: bold;">
                            <td style="padding: 12px 15px; text-align: right;" colspan="3">TOTAL</td>
                            <td style="padding: 12px 15px; text-align: right;"><?= number_format($total_montant, 2, ',', ' ') ?> DH</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
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

<!-- Script pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour l'évolution des factures
        var factures_par_jour = <?= json_encode($factures_par_jour) ?>;
        
        // Graphique d'évolution des factures
        var optionsEvolution = {
            series: [{
                name: 'Factures',
                type: 'column',
                data: factures_par_jour.map(function(item) { return item.count; })
            }, {
                name: 'Montant (DH)',
                type: 'line',
                data: factures_par_jour.map(function(item) { return item.amount; })
            }],
            chart: {
                height: 300,
                type: 'line',
                toolbar: {
                    show: false
                }
            },
            stroke: {
                width: [0, 4],
                curve: 'smooth'
            },
            colors: ['#0a2558', '#2a9d8f'],
            xaxis: {
                categories: factures_par_jour.map(function(item) { return item.date; }),
                labels: {
                    rotate: -45,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Nombre de Factures'
                    },
                    labels: {
                        formatter: function(val) {
                            return val.toFixed(0);
                        }
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Montant (DH)'
                    },
                    labels: {
                        formatter: function(val) {
                            return val.toFixed(0) + ' DH';
                        }
                    }
                }
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(y, { series, seriesIndex, dataPointIndex, w }) {
                        if (seriesIndex === 0) {
                            return y.toFixed(0) + ' factures';
                        } else {
                            return y.toFixed(2) + ' DH';
                        }
                    }
                }
            },
            legend: {
                position: 'top'
            },
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 5
            }
        };
        
        var chartEvolution = new ApexCharts(document.querySelector("#chart-evolution"), optionsEvolution);
        chartEvolution.render();
    });
</script>

<?php
include 'pied.php';
?>