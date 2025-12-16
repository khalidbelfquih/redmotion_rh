<?php
include '../config/connexion.php';
include '../model/conge_functions.php';

// Récupérer l'ID du congé
$id_conge = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_conge) {
    header('Location: conges.php');
    exit;
}

// Récupérer les détails du congé
$conge = getCongeById($id_conge);

if (!$conge) {
    header('Location: conges.php');
    exit;
}

// Récupérer les infos de la société
$stmt = $connexion->query("SELECT * FROM societe_info LIMIT 1");
$societe = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permission_<?= $conge['nom'] ?>_<?= $conge['prenom'] ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #b91c1c; /* Red Dark */
            --secondary-color: #1f2937; /* Gray Dark */
            --accent-color: #ef4444; /* Red Light */
            --text-color: #333;
            --light-gray: #f9fafb;
            --border-color: #e5e7eb;
            --gradient: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 15px;
            line-height: 1.4;
            color: var(--text-color);
            font-size: 14px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
            min-height: calc(100vh - 30px);
            display: flex;
            flex-direction: column;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient);
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--gradient);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(93, 64, 55, 0.3);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .print-btn:hover {
            transform: translateY(-1px);
        }

        /* En-tête moderne */
        .header {
            background: var(--gradient);
            color: white;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .company-info h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .company-info p {
            font-size: 13px;
            opacity: 0.9;
        }

        .document-info {
            text-align: right;
            background: rgba(255, 255, 255, 0.15);
            padding: 15px;
            border-radius: 6px;
        }

        .document-number {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .document-date {
            font-size: 13px;
            opacity: 0.9;
        }

        /* Corps */
        .content {
            padding: 20px;
            flex: 1;
        }

        /* Section client et vente en 2 colonnes */
        .client-sale-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-card {
            background: var(--light-gray);
            border-radius: 6px;
            padding: 15px;
            border-left: 4px solid var(--secondary-color);
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title i {
            color: var(--secondary-color);
            font-size: 16px;
        }

        .info-list {
            font-size: 13px;
            line-height: 1.5;
            position: relative;
        }

        .info-list div {
            margin-bottom: 5px;
        }

        .info-list strong {
            color: var(--primary-color);
            display: inline-block;
            min-width: 100px;
        }
        
        .signature-img {
            max-width: 150px;
            max-height: 80px;
            position: absolute;
            top: 10px;
            left: 20px;
            transform: rotate(-10deg);
            opacity: 0.8;
            mix-blend-mode: multiply;
        }

        /* Tableau des produits */
        .products-section {
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            margin-bottom: 15px;
        }

        .table-header {
            background: var(--gradient);
            color: white;
            padding: 15px;
            text-align: center;
        }

        .table-header h3 {
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
        }

        .products-table th,
        .products-table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            font-size: 12px;
        }

        .products-table th {
            background: #e7eeff;
            font-weight: 600;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .products-table tbody tr:hover {
            background: var(--light-gray);
        }

        /* Footer */
        .footer {
            margin-top: auto;
            text-align: center;
            padding: 15px;
            background: var(--primary-color);
            color: white;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left {
            font-weight: 700;
        }

        .footer-right {
            display: flex;
            gap: 15px;
            font-size: 11px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .client-sale-section {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .document-info {
                text-align: center;
            }
        }

        /* IMPRESSION */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0.2in !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                box-shadow: none !important;
            }

            html, body {
                font-size: 9pt !important;
                line-height: 1.2 !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                height: auto !important;
            }

            .print-btn {
                display: none !important;
            }

            .container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                min-height: auto !important;
                display: block !important;
            }

            .container::before {
                background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%) !important;
                height: 3px !important;
            }

            .header {
                background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%) !important;
                padding: 12px !important;
            }

            .company-info h1 {
                font-size: 16pt !important;
                color: white !important;
            }

            .company-info p {
                font-size: 8pt !important;
                color: white !important;
            }

            .document-number {
                font-size: 14pt !important;
                color: white !important;
            }

            .document-date {
                font-size: 9pt !important;
                color: white !important;
            }

            .content {
                padding: 10px !important;
            }

            .client-sale-section {
                gap: 8px !important;
                margin-bottom: 8px !important;
            }

            .info-card {
                background: #f9fafb !important;
                padding: 8px !important;
                border-left: 3px solid #1f2937 !important;
            }

            .card-title {
                font-size: 11pt !important;
                margin-bottom: 5px !important;
                color: #b91c1c !important;
            }

            .card-title i {
                color: #1f2937 !important;
            }

            .info-list {
                font-size: 8pt !important;
            }

            .info-list strong {
                color: #b91c1c !important;
            }

            .table-header {
                background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%) !important;
                padding: 8px !important;
            }

            .table-header h3 {
                font-size: 11pt !important;
                color: white !important;
            }

            .products-table th,
            .products-table td {
                padding: 6px 8px !important;
                font-size: 8pt !important;
                margin: 0 !important;
            }
            .products-table {
                font-size: 8pt !important;
            }

            .products-table th {
                background: #f9fafb !important;
                color: #b91c1c !important;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimer
    </button>

    <div class="container">
        <div class="header">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">
                        <?php if(!empty($societe['logo_path']) && file_exists(__DIR__ . '/../' . $societe['logo_path'])): ?>
                            <img src="../<?= $societe['logo_path'] ?>" alt="Logo">
                        <?php else: ?>
                            <i class="fas fa-birthday-cake" style="font-size: 30px; color: white;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="company-info">
                        <h1><?= !empty($societe['nom']) ? htmlspecialchars($societe['nom']) : 'RED MOTION' ?></h1>
                        <p><?= !empty($societe['adresse']) ? htmlspecialchars($societe['adresse']) : 'Audio Visual et Production Videos' ?></p>
                        <p>Tél: <?= !empty($societe['telephone']) ? htmlspecialchars($societe['telephone']) : '' ?> | Email: <?= !empty($societe['email']) ? htmlspecialchars($societe['email']) : '' ?></p>
                    </div>
                </div>
                <div class="document-info">
                    <div class="document-number">
                        <i class="fas fa-file-contract"></i> TITRE DE PERMISSION
                    </div>
                    <div class="document-date">
                        Date demande: <?= $date_demande ?><br>
                        Réf: <?= $reference ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="client-sale-section">
                <div class="info-card">
                    <div class="card-title">
                        <i class="fas fa-user-tie"></i> Informations Salarié
                    </div>
                    <div class="info-list">
                        <div><strong>Nom & Prénom:</strong> <?= $conge['nom'] . ' ' . $conge['prenom'] ?></div>
                        <div><strong>Matricule:</strong> EMP-<?= str_pad($conge['id_employe'], 4, '0', STR_PAD_LEFT) ?></div>
                        <div><strong>Email:</strong> <?= $conge['email'] ?></div>
                        <div><strong>Poste:</strong> <?= $conge['poste'] ?></div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-title">
                        <i class="fas fa-info-circle"></i> Détails du Congé
                    </div>
                    <div class="info-list">
                        <div><strong>Type:</strong> <?= $conge['type_conge'] ?></div>
                        <div><strong>Date Début:</strong> <?= date('d/m/Y', strtotime($conge['date_debut'])) ?></div>
                        <div><strong>Date Fin:</strong> <?= date('d/m/Y', strtotime($conge['date_fin'])) ?></div>
                        <div><strong>Durée:</strong> 
                            <?php
                            $debut = new DateTime($conge['date_debut']);
                            $fin = new DateTime($conge['date_fin']);
                            echo $debut->diff($fin)->days + 1 . ' jours';
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="products-section">
                <div class="table-header">
                    <h3><i class="fas fa-list-alt"></i> Motif & Validation</h3>
                </div>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Motif de la demande</strong></td>
                            <td><?= nl2br($conge['motif']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Statut actuel</strong></td>
                            <td>
                                <span style="font-weight: bold; color: <?= $conge['statut'] == 'Approuvé' ? 'green' : 'red' ?>;">
                                    <?= strtoupper($conge['statut']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php if (!empty($conge['commentaire_admin'])): ?>
                        <tr>
                            <td><strong>Commentaire Admin</strong></td>
                            <td><?= $conge['commentaire_admin'] ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="client-sale-section" style="margin-top: 30px;">
                <div class="info-card" style="border-left-color: var(--primary-color);">
                    <div class="card-title">
                        <i class="fas fa-signature"></i> Signature Salarié
                    </div>
                    <div class="info-list">
                        <div style="margin-top: 10px; font-style: italic;">(Précédée de la mention "Lu et approuvé")</div>
                        <div style="height: 60px;"></div>
                    </div>
                </div>

                <div class="info-card" style="border-left-color: var(--primary-color);">
                    <div class="card-title">
                        <i class="fas fa-stamp"></i> Cachet Employeur
                    </div>
                    <div class="info-list" style="min-height: 80px;">
                        <div style="margin-bottom: 5px;"><strong>Signature:</strong></div>
                        
                        <?php 
                        // Vérifier si l'utilisateur veut la signature (par défaut OUI)
                        $show_signature = !isset($_GET['signed']) || $_GET['signed'] == 1;
                        
                        if($conge['statut'] == 'Approuvé' && !empty($societe['cachet_path']) && $show_signature): 
                        ?>
                            <!-- Affichage du cachet électronique -->
                             <?php 
                                // Nettoyer le chemin pour l'affichage (supprimer ../public/ si nécessaire ou ajuster)
                                // Le chemin stocké est par exemple "uploads/societe/cachet_123.png"
                                $cachetSrc = $societe['cachet_path']; 
                                // Fix for PHP < 8.0 (str_starts_with not available)
                                if (strpos($cachetSrc, '../') !== 0) {
                                     $cachetSrc = '../' . $cachetSrc;
                                }
                             ?>
                            <img src="<?= $cachetSrc ?>" alt="Cachet" class="signature-img">
                        <?php else: ?>
                            <div style="height: 50px;"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-left">
                <?= !empty($societe['nom']) ? htmlspecialchars($societe['nom']) : 'RED MOTION' ?>
            </div>
            <div class="footer-right">
                <span><i class="fas fa-map-marker-alt"></i> <?= !empty($societe['adresse']) ? substr(htmlspecialchars($societe['adresse']), 0, 30) : '4A rue Ahmed amine' ?></span>
                <span><i class="fas fa-phone"></i> <?= !empty($societe['telephone']) ? htmlspecialchars($societe['telephone']) : '+212 6 61 55 33 94' ?></span>
                <span><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y') ?></span>
            </div>
        </div>
    </div>
</body>
</html>
