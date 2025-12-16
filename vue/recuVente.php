<?php
// Fichier: recuVente.php - Version moderne avec design professionnel
include '../model/connexion.php';

// Si vous avez un fichier spécifique pour les fonctions de commande
if (file_exists('../model/fonction.php')) {
    include '../model/fonction.php';
} 

// Vérifier si l'ID de la vente est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Erreur: Aucun ID de vente spécifié.'); window.close();</script>";
    exit;
}

$id_vente = intval($_GET['id']);

// Récupérer les informations de la vente
$sql_vente = "SELECT v.*, c.nom, c.prenom, c.telephone, c.adresse, c.email, c.mutuelle, c.numero_secu, 
              a.nom_article, a.marque, a.modele, a.prix_unitaire, 
              p.montant_total, p.montant_paye, p.reste_a_payer, p.mode_paiement, p.reference_paiement, p.date_paiement, p.statut
              FROM vente v
              LEFT JOIN client c ON v.id_client = c.id
              LEFT JOIN article a ON v.id_article = a.id
              LEFT JOIN paiement p ON v.id = p.id_vente
              WHERE v.id = ?";

$req_vente = $connexion->prepare($sql_vente);
$req_vente->execute([$id_vente]);
$vente = $req_vente->fetch(PDO::FETCH_ASSOC);

if (!$vente) {
    echo "<script>alert('Erreur: Vente non trouvée.'); window.close();</script>";
    exit;
}

// Récupérer les détails de la prescription si elle existe
$prescription = null;
if (!empty($vente['id_prescription'])) {
    $sql_prescription = "SELECT p.*, 
                       od.sphere as od_sphere, od.cylindre as od_cylindre, od.axe as od_axe, od.addition as od_addition, od.ecart_pupillaire as od_ecart,
                       og.sphere as og_sphere, og.cylindre as og_cylindre, og.axe as og_axe, og.addition as og_addition, og.ecart_pupillaire as og_ecart
                       FROM prescription p
                       LEFT JOIN details_prescription od ON p.id = od.id_prescription AND od.type = 'droite'
                       LEFT JOIN details_prescription og ON p.id = og.id_prescription AND og.type = 'gauche'
                       WHERE p.id = ?";
    $req_prescription = $connexion->prepare($sql_prescription);
    $req_prescription->execute([$vente['id_prescription']]);
    $prescription = $req_prescription->fetch(PDO::FETCH_ASSOC);
}

// Récupérer les lignes de vente
$lignes_vente = [];
$sql_lignes = "SELECT lv.*, a.nom_article, a.marque, a.modele, a.prix_unitaire
              FROM ligne_vente lv
              LEFT JOIN article a ON lv.id_article = a.id
              WHERE lv.id_vente = ?";
$req_lignes = $connexion->prepare($sql_lignes);
$req_lignes->execute([$id_vente]);
$lignes_vente = $req_lignes->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les détails optiques
$details_optiques_all = [];
if (!empty($lignes_vente)) {
    $sql_details_optiques = "SELECT do.*, lv.id as id_ligne_vente
                            FROM details_optique do
                            JOIN ligne_vente lv ON do.id_ligne_vente = lv.id
                            WHERE lv.id_vente = ?";
    $req_details_optiques = $connexion->prepare($sql_details_optiques);
    $req_details_optiques->execute([$id_vente]);
    $details_optiques_all = $req_details_optiques->fetchAll(PDO::FETCH_ASSOC);
}

// Organiser les détails optiques par ligne de vente
$details_optiques = [];
foreach ($details_optiques_all as $detail) {
    $details_optiques[$detail['id_ligne_vente']] = $detail;
}

// Récupérer les échéances de paiement si crédit
$echeances = [];
if ($vente['mode_paiement'] === 'credit' && !empty($vente['reste_a_payer'])) {
    $sql_echeance = "SELECT e.* FROM echeancier e
                   JOIN paiement p ON e.id_paiement = p.id
                   WHERE p.id_vente = ?
                   ORDER BY e.date_echeance ASC";
    $req_echeance = $connexion->prepare($sql_echeance);
    $req_echeance->execute([$id_vente]);
    $echeances = $req_echeance->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les informations de garantie
$garantie = null;
$sql_garantie = "SELECT * FROM garantie WHERE id_vente = ? LIMIT 1";
$req_garantie = $connexion->prepare($sql_garantie);
$req_garantie->execute([$id_vente]);
$garantie = $req_garantie->fetch(PDO::FETCH_ASSOC);

// Formatage du numéro de référence de la vente
$reference_vente = sprintf('V%05d', $id_vente);
$date_formattee = date('d/m/Y', strtotime($vente['date_vente']));

// Déterminer si c'est une facture ou un reçu
$type_document = ($vente['statut'] === 'complet') ? 'REÇU' : 'FACTURE';

// Formatage du mode de paiement pour affichage
$modes_paiement = [
    'especes' => 'Espèces',
    'carte_bancaire' => 'Carte bancaire',
    'cheque' => 'Chèque',
    'credit' => 'Crédit'
];
$mode_paiement_affichage = isset($modes_paiement[$vente['mode_paiement']]) ? $modes_paiement[$vente['mode_paiement']] : $vente['mode_paiement'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $type_document ?>_<?= $reference_vente ?>_<?= $vente['nom'] ?>_<?= $vente['prenom'] ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0a2558;
            --secondary-color: #2a9d8f;
            --accent-color: #f4a261;
            --text-color: #333;
            --light-gray: #f8f9fc;
            --border-color: #e3e6f0;
            --gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
            box-shadow: 0 4px 15px rgba(10, 37, 88, 0.3);
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
        }

        .logo img {
            max-width: 45px;
            max-height: 45px;
            border-radius: 50%;
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
        }

        .info-list div {
            margin-bottom: 5px;
        }

        .info-list strong {
            color: var(--primary-color);
            display: inline-block;
            min-width: 80px;
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

        .products-table th:last-child,
        .products-table td:last-child {
            text-align: right;
        }

        .products-table tbody tr:hover {
            background: var(--light-gray);
        }

        .optique-details {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
            padding-left: 10px;
            border-left: 2px solid #ddd;
        }

        .optique-details div {
            margin-bottom: 3px;
        }

        .total-row {
            background: var(--primary-color) !important;
            color: white !important;
            font-weight: 700;
        }

        .total-row td {
            border-bottom: none !important;
            font-size: 14px;
        }

        /* Section prescription */
        .prescription-section {
            background: linear-gradient(135deg, #e7eeff 0%, #f0f7ff 100%);
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary-color);
        }

        .prescription-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .prescription-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .prescription-table th,
        .prescription-table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ccc;
        }

        .prescription-table th {
            background: rgba(10, 37, 88, 0.1);
            font-weight: 600;
            color: var(--primary-color);
        }

        .prescription-table tbody tr:first-child {
            background: rgba(42, 157, 143, 0.05);
        }

        .prescription-table tbody tr:last-child {
            background: rgba(42, 157, 143, 0.1);
        }

        /* Section paiement et échéancier */
        .payment-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .payment-card {
            background: var(--light-gray);
            border-radius: 6px;
            padding: 15px;
            border-left: 4px solid var(--secondary-color);
        }

        .schedule-card {
            background: var(--light-gray);
            border-radius: 6px;
            padding: 15px;
            border-left: 4px solid var(--primary-color);
        }

        .payment-table {
            width: 100%;
            font-size: 12px;
        }

        .payment-table td {
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }

        .payment-table td:first-child {
            font-weight: 600;
            color: var(--primary-color);
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .schedule-table th,
        .schedule-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }

        .schedule-table th {
            background: rgba(10, 37, 88, 0.1);
            color: var(--primary-color);
            font-weight: 600;
        }

        .status-paye { color: #00b894; font-weight: 600; }
        .status-a-venir { color: #0984e3; font-weight: 600; }
        .status-retard { color: #e17055; font-weight: 600; }

        /* Section garantie */
        .warranty-section {
            background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--accent-color);
        }

        .warranty-title {
            font-size: 16px;
            font-weight: 700;
            color: #d63031;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .warranty-table {
            width: 100%;
            font-size: 12px;
        }

        .warranty-table td {
            padding: 5px 0;
            color: #2d3436;
        }

        .warranty-table td:first-child {
            font-weight: 600;
            color: #d63031;
            width: 25%;
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

        .footer-contact {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .footer-contact i {
            color: var(--accent-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .client-sale-section,
            .payment-section {
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

        /* IMPRESSION - UNE SEULE PAGE */
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
                animation: none !important;
                transition: none !important;
            }

            html, body {
                font-size: 9pt !important;
                line-height: 1.2 !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                height: auto !important;
                overflow: visible !important;
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
                background: linear-gradient(135deg, #0a2558 0%, #2a9d8f 100%) !important;
                height: 3px !important;
            }

            .header {
                background: linear-gradient(135deg, #0a2558 0%, #2a9d8f 100%) !important;
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
                flex: none !important;
            }

            .client-sale-section,
            .payment-section {
                gap: 8px !important;
                margin-bottom: 8px !important;
            }

            .info-card,
            .payment-card,
            .schedule-card {
                background: #f8f9fc !important;
                padding: 8px !important;
                border-left: 3px solid #2a9d8f !important;
            }

            .card-title,
            .warranty-title {
                font-size: 11pt !important;
                margin-bottom: 5px !important;
            }

            .card-title {
                color: #0a2558 !important;
            }

            .card-title i {
                color: #2a9d8f !important;
            }

            .info-list {
                font-size: 8pt !important;
            }

            .info-list strong {
                color: #0a2558 !important;
            }

            .products-section {
                margin-bottom: 8px !important;
            }

            .table-header {
                background: linear-gradient(135deg, #0a2558 0%, #2a9d8f 100%) !important;
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
            }

            .products-table th {
                background: #e7eeff !important;
                color: #0a2558 !important;
            }

            .optique-details {
                font-size: 7pt !important;
            }

            .total-row {
                background: #0a2558 !important;
                color: white !important;
            }

            .prescription-section {
                background: linear-gradient(135deg, #e7eeff 0%, #f0f7ff 100%) !important;
                padding: 8px !important;
                margin-bottom: 8px !important;
                border-left: 3px solid #0a2558 !important;
            }

            .prescription-title {
                font-size: 11pt !important;
                color: #0a2558 !important;
            }

            .prescription-table th,
            .prescription-table td {
                padding: 4px !important;
                font-size: 8pt !important;
            }

            .prescription-table th {
                background: rgba(10, 37, 88, 0.1) !important;
                color: #0a2558 !important;
            }

            .payment-table {
                font-size: 8pt !important;
            }

            .payment-table td:first-child {
                color: #0a2558 !important;
            }

            .schedule-table {
                font-size: 7pt !important;
            }

            .schedule-table th {
                background: rgba(10, 37, 88, 0.1) !important;
                color: #0a2558 !important;
            }

            .warranty-section {
                background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%) !important;
                padding: 8px !important;
                border-left: 3px solid #f4a261 !important;
            }

            .warranty-title {
                font-size: 11pt !important;
                color: #d63031 !important;
            }

            .warranty-table {
                font-size: 8pt !important;
            }

            .warranty-table td:first-child {
                color: #d63031 !important;
            }

            .footer {
                background: #0a2558 !important;
                color: white !important;
                padding: 8px !important;
                font-size: 8pt !important;
            }

            .footer-left {
                color: white !important;
            }

            .footer-right {
                font-size: 7pt !important;
            }

            .footer-contact {
                color: white !important;
            }

            .footer-contact i {
                color: #f4a261 !important;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="printDocument()">
        <i class="fas fa-print"></i>
        Imprimer
    </button>
    
    <div class="container">
        <!-- En-tête moderne -->
        <div class="header">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">
                        <img src="../public/images/logo.png" alt="Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <i class="fas fa-receipt" style="display: none; font-size: 24px; color: white;"></i>
                    </div>
                    <div class="company-info">
                        <h1>VisionKa Optique</h1>
                        <p>11 Av. Al Abtal, Rabat</p>
                        <p>Tél: +212 6 50 78 96 02</p>
                    </div>
                </div>
                
                <div class="document-info">
                    <div class="document-number">
                        <i class="fas fa-file-invoice"></i>
                        <?= $type_document ?> N° <?= $reference_vente ?>
                    </div>
                    <div class="document-date">
                     Émis le <?= $date_formattee ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="content">
            <!-- Client et Vente en 2 colonnes -->
            <div class="client-sale-section">
                <div class="info-card">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i>
                        Client
                    </h3>
                    <div class="info-list">
                        <div><strong>Nom :</strong> <?= htmlspecialchars($vente['nom']) ?> <?= htmlspecialchars($vente['prenom']) ?></div>
                        <div><strong>Tél :</strong> <?= htmlspecialchars($vente['telephone']) ?></div>
                        <?php if (!empty($vente['email'])): ?>
                        <div><strong>Email :</strong> <?= htmlspecialchars($vente['email']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($vente['mutuelle'])): ?>
                        <div><strong>Mutuelle :</strong> <?= htmlspecialchars($vente['mutuelle']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart"></i>
                        Vente
                    </h3>
                    <div class="info-list">
                        <div><strong>Date :</strong> <?= $date_formattee ?></div>
                        <div><strong>Référence :</strong> <?= $reference_vente ?></div>
                        <?php if (!empty($vente['date_livraison'])): ?>
                        <div><strong>Livraison :</strong> <?= date('d/m/Y', strtotime($vente['date_livraison'])) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($vente['commentaires'])): ?>
                        <div><strong>Note :</strong> <?= htmlspecialchars($vente['commentaires']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Tableau des produits -->
            <div class="products-section">
                <div class="table-header">
                    <h3>
                        <i class="fas fa-list"></i>
                        Détails des Produits
                    </h3>
                </div>
                
                <table class="products-table">
                    <thead>
                        <tr>
                            <th width="45%">Description</th>
                            <th width="15%">Quantité</th>
                            <th width="20%">Prix unitaire</th>
                            <th width="20%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($lignes_vente) > 0): ?>
                            <?php 
                            $total_calcule = 0;
                            foreach ($lignes_vente as $ligne): 
                                $total_calcule += $ligne['prix_total'];
                            ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($ligne['nom_article']) ?></strong>
                                    <?php if (!empty($ligne['marque'])): ?>
                                    - <?= htmlspecialchars($ligne['marque']) ?>
                                    <?php endif; ?>
                                    <?php if (!empty($ligne['modele'])): ?>
                                    <?= htmlspecialchars($ligne['modele']) ?>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Afficher les détails optiques s'ils existent
                                    if (isset($details_optiques[$ligne['id']])):
                                        $detail = $details_optiques[$ligne['id']];
                                    ?>
                                    <div class="optique-details">
                                        <?php if (!empty($detail['type_monture'])): ?>
                                        <div><strong>Monture:</strong> <?= htmlspecialchars($detail['type_monture']) ?></div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($detail['ref_monture'])): ?>
                                        <div><strong>Réf:</strong> <?= htmlspecialchars($detail['ref_monture']) ?></div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($detail['type_verre_od']) || !empty($detail['type_verre_og'])): ?>
                                        <div>
                                            <strong>Verres:</strong> 
                                            <?php if (!empty($detail['type_verre_od'])): ?>
                                            OD: <?= htmlspecialchars($detail['type_verre_od']) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($detail['type_verre_og'])): ?>
                                            <?= (!empty($detail['type_verre_od'])) ? ' | ' : '' ?>OG: <?= htmlspecialchars($detail['type_verre_og']) ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($detail['traitement'])): ?>
                                        <div><strong>Traitement:</strong> <?= htmlspecialchars($detail['traitement']) ?></div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($detail['eip_od']) || !empty($detail['eip_og'])): ?>
                                        <div>
                                            <strong>EIP:</strong> 
                                            <?php if (!empty($detail['eip_od'])): ?>
                                            OD: <?= $detail['eip_od'] ?>mm
                                            <?php endif; ?>
                                            <?php if (!empty($detail['eip_og'])): ?>
                                            <?= (!empty($detail['eip_od'])) ? ' | ' : '' ?>OG: <?= $detail['eip_og'] ?>mm
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($detail['hauteur_od']) || !empty($detail['hauteur_og'])): ?>
                                        <div>
                                            <strong>Hauteur:</strong> 
                                            <?php if (!empty($detail['hauteur_od'])): ?>
                                            OD: <?= $detail['hauteur_od'] ?>mm
                                            <?php endif; ?>
                                            <?php if (!empty($detail['hauteur_og'])): ?>
                                            <?= (!empty($detail['hauteur_od'])) ? ' | ' : '' ?>OG: <?= $detail['hauteur_og'] ?>mm
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $ligne['quantite'] ?></td>
                                <td><?= number_format($ligne['prix_total'] / $ligne['quantite'], 2, ',', ' ') ?> DH</td>
                                <td><?= number_format($ligne['prix_total'], 2, ',', ' ') ?> DH</td>
                            </tr>
                            
                            <?php 
                            // Ajouter une ligne séparée pour les verres s'ils ont un prix
                            if (isset($details_optiques[$ligne['id']]) && !empty($details_optiques[$ligne['id']]['prix_verres']) && $details_optiques[$ligne['id']]['prix_verres'] > 0):
                                $detail = $details_optiques[$ligne['id']];
                                $prix_verres = floatval($detail['prix_verres']);
                                $total_calcule += $prix_verres;
                            ?>
                            <tr>
                                <td>
                                    <strong>Verres correcteurs</strong>
                                    <div class="optique-details">
                                        <?php if (!empty($detail['type_verre_od']) || !empty($detail['type_verre_og'])): ?>
                                        <div>
                                            <strong>Types:</strong> 
                                            <?php if (!empty($detail['type_verre_od'])): ?>
                                            OD: <?= htmlspecialchars($detail['type_verre_od']) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($detail['type_verre_og'])): ?>
                                            <?= (!empty($detail['type_verre_od'])) ? ' | ' : '' ?>OG: <?= htmlspecialchars($detail['type_verre_og']) ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($detail['traitement'])): ?>
                                        <div><strong>Traitement:</strong> <?= htmlspecialchars($detail['traitement']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>1</td>
                                <td><?= number_format($prix_verres, 2, ',', ' ') ?> DH</td>
                                <td><?= number_format($prix_verres, 2, ',', ' ') ?> DH</td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php $total_calcule = $vente['prix']; ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($vente['nom_article']) ?></strong>
                                    <?php if (!empty($vente['marque'])): ?>
                                    - <?= htmlspecialchars($vente['marque']) ?>
                                    <?php endif; ?>
                                    <?php if (!empty($vente['modele'])): ?>
                                    <?= htmlspecialchars($vente['modele']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>1</td>
                                <td><?= number_format($vente['prix'], 2, ',', ' ') ?> DH</td>
                                <td><?= number_format($vente['prix'], 2, ',', ' ') ?> DH</td>
                            </tr>
                        <?php endif; ?>
                        
                        <!-- Ligne total -->
                        <tr class="total-row">
                            <td colspan="3"><strong>TOTAL</strong></td>
                            <td><strong><?= number_format($vente['montant_total'] ?? $total_calcule, 2, ',', ' ') ?> DH</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Prescription si disponible -->
            <?php if ($prescription): ?>
            <div class="prescription-section">
                <h3 class="prescription-title">
                    <i class="fas fa-file-medical"></i>
                    Prescription Médicale
                </h3>
                <table class="prescription-table">
                    <thead>
                        <tr>
                            <th rowspan="2">Œil</th>
                            <th colspan="4">Correction</th>
                        </tr>
                        <tr>
                            <th>Sphère</th>
                            <th>Cylindre</th>
                            <th>Axe</th>
                            <th>Addition</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>OD (Droit)</strong></td>
                            <td><?= $prescription['od_sphere'] ?? '-' ?></td>
                            <td><?= $prescription['od_cylindre'] ?? '-' ?></td>
                            <td><?= $prescription['od_axe'] ?? '-' ?></td>
                            <td><?= $prescription['od_addition'] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td><strong>OG (Gauche)</strong></td>
                            <td><?= $prescription['og_sphere'] ?? '-' ?></td>
                            <td><?= $prescription['og_cylindre'] ?? '-' ?></td>
                            <td><?= $prescription['og_axe'] ?? '-' ?></td>
                            <td><?= $prescription['og_addition'] ?? '-' ?></td>
                        </tr>
                    </tbody>
                </table>
                <?php if (!empty($prescription['medecin'])): ?>
                <p style="margin-top: 8px; font-size: 12px;"><strong>Médecin:</strong> <?= htmlspecialchars($prescription['medecin']) ?> | <strong>Date:</strong> <?= date('d/m/Y', strtotime($prescription['date_prescription'])) ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Paiement et Échéancier -->
            <div class="payment-section">
                <div class="payment-card">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card"></i>
                        Paiement
                    </h3>
                    <table class="payment-table">
                        <tr>
                            <td>Mode de paiement :</td>
                            <td><?= $mode_paiement_affichage ?></td>
                        </tr>
                        <?php if (!empty($vente['reference_paiement'])): ?>
                        <tr>
                            <td>Référence :</td>
                            <td><?= htmlspecialchars($vente['reference_paiement']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Montant total :</td>
                            <td><strong><?= number_format($vente['montant_total'] ?? $vente['prix'], 2, ',', ' ') ?> DH</strong></td>
                        </tr>
                        <tr>
                            <td>Montant payé :</td>
                            <td><strong><?= number_format($vente['montant_paye'] ?? $vente['prix'], 2, ',', ' ') ?> DH</strong></td>
                        </tr>
                        <?php if (!empty($vente['reste_a_payer']) && $vente['reste_a_payer'] > 0): ?>
                        <tr>
                            <td>Reste à payer :</td>
                            <td><strong style="color: #e17055;"><?= number_format($vente['reste_a_payer'], 2, ',', ' ') ?> DH</strong></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                
                <?php if (count($echeances) > 0): ?>
                <div class="schedule-card">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i>
                        Échéancier
                    </h3>
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($echeances as $echeance): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($echeance['date_echeance'])) ?></td>
                                <td><?= number_format($echeance['montant'], 2, ',', ' ') ?> DH</td>
                                <td>
                                    <?php 
                                    switch ($echeance['statut']) {
                                        case 'paye':
                                            echo '<span class="status-paye">Payé</span>';
                                            break;
                                        case 'a_venir':
                                            echo '<span class="status-a-venir">À venir</span>';
                                            break;
                                        case 'en_retard':
                                            echo '<span class="status-retard">En retard</span>';
                                            break;
                                        default:
                                            echo htmlspecialchars($echeance['statut']);
                                            break;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Garantie si disponible -->
            <?php if ($garantie): ?>
            <div class="warranty-section">
                <h3 class="warranty-title">
                    <i class="fas fa-shield-alt"></i>
                    Informations de Garantie
                </h3>
                <table class="warranty-table">
                    <tr>
                        <td>Type :</td>
                        <td>
                            <?php 
                            switch ($garantie['type_garantie']) {
                                case 'monture':
                                    echo 'Monture uniquement';
                                    break;
                                case 'verres':
                                    echo 'Verres uniquement';
                                    break;
                                case 'monture_et_verres':
                                    echo 'Monture et verres';
                                    break;
                                default:
                                    echo htmlspecialchars($garantie['type_garantie']);
                                    break;
                            }
                            ?>
                        </td>
                        <td>Période :</td>
                        <td>Du <?= date('d/m/Y', strtotime($garantie['date_debut'])) ?> au <?= date('d/m/Y', strtotime($garantie['date_fin'])) ?></td>
                    </tr>
                    <?php if (!empty($garantie['conditions'])): ?>
                    <tr>
                        <td>Conditions :</td>
                        <td colspan="3"><?= htmlspecialchars($garantie['conditions']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-left">
                <strong>Merci pour votre confiance !</strong>
            </div>
            <div class="footer-right">
                <div class="footer-contact">
                    <i class="fas fa-map-marker-alt"></i>
                    11 Av. Al Abtal, Rabat
                </div>
                <div class="footer-contact">
                    <i class="fas fa-phone"></i>
                    +212 6 50 78 96 02
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const venteId = '<?= $reference_vente ?>';
            const clientNom = '<?= $vente['nom'] ?>';
            const clientPrenom = '<?= $vente['prenom'] ?>';
            const fileName = `${venteId}_${clientNom}_${clientPrenom}`;
            document.title = fileName;
        });

        function printDocument() {
            const venteId = '<?= $reference_vente ?>';
            const clientNom = '<?= $vente['nom'] ?>';
            const clientPrenom = '<?= $vente['prenom'] ?>';
            const fileName = `<?= $type_document ?>_${venteId}_${clientNom}_${clientPrenom}`;
            document.title = fileName;
            window.print();
        }
        
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === 'auto') {
                setTimeout(() => printDocument(), 500);
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                printDocument();
            }
        });
        
        if ('onbeforeprint' in window) {
            window.onbeforeprint = function() {
                const venteId = '<?= $reference_vente ?>';
                const clientNom = '<?= $vente['nom'] ?>';
                const clientPrenom = '<?= $vente['prenom'] ?>';
                const fileName = `<?= $type_document ?>_${venteId}_${clientNom}_${clientPrenom}`;
                document.title = fileName;
            };
        }
    </script>
</body>
</html>