<?php
include '../model/hr_functions.php';

if (!isset($_GET['id'])) {
    die("ID employé manquant");
}

$emp = getEmploye($_GET['id']);
if (!$emp) {
    die("Employé introuvable");
}

// Récupérer les infos de la société
$stmt = $connexion->query("SELECT * FROM societe_info LIMIT 1");
$societe = $stmt->fetch(PDO::FETCH_ASSOC);

$date_jour = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attestation_Travail_<?= $emp['nom'] ?>_<?= $emp['prenom'] ?></title>
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
            line-height: 1.6;
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
            font-size: 24px;
            color: white;
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
            padding: 40px;
            flex: 1;
            font-size: 16px;
        }

        .attestation-body {
            text-align: justify;
            line-height: 1.8;
            margin-top: 20px;
        }

        .attestation-body p {
            margin-bottom: 20px;
        }

        .highlight {
            font-weight: 700;
            color: var(--primary-color);
        }

        .employee-details {
            background: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid var(--secondary-color);
            margin: 30px 0;
        }

        .employee-details p {
            margin-bottom: 10px;
        }
        
        .employee-details p:last-child {
            margin-bottom: 0;
        }

        /* Footer */
        .footer-signature {
            margin-top: 60px;
            display: flex;
            justify-content: flex-end;
            padding-right: 40px;
            position: relative;
        }

        .signature-box {
            text-align: center;
            width: 250px;
            position: relative;
        }

        .signature-title {
            font-weight: 700;
            margin-bottom: 80px;
            color: var(--primary-color);
        }
        
        .signature-img {
            max-width: 180px;
            max-height: 100px;
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%) rotate(-5deg);
            opacity: 0.9;
            mix-blend-mode: multiply;
        }

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
                font-size: 11pt !important;
                line-height: 1.5 !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
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
                padding: 20px !important;
            }

            .employee-details {
                background: #f8f9fc !important;
                border-left: 3px solid #2a9d8f !important;
            }

            .highlight {
                color: #0a2558 !important;
            }

            .footer {
                background: #b91c1c !important;
                color: white !important;
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
                            <i class="fas fa-building"></i>
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
                        <i class="fas fa-briefcase"></i>
                        ATTESTATION DE TRAVAIL
                    </div>
                    <div class="document-date">
                        Date: <?= $date_jour ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="attestation-body">
                <p>Nous soussignés, <strong><?= !empty($societe['nom']) ? htmlspecialchars($societe['nom']) : 'RED MOTION' ?></strong>, société sise au <?= !empty($societe['adresse']) ? htmlspecialchars($societe['adresse']) : 'Casablanca' ?>, certifions par la présente que :</p>
                
                <div class="employee-details">
                    <p><strong>M./Mme :</strong> <span class="highlight"><?= strtoupper($emp['nom']) ?> <?= $emp['prenom'] ?></span></p>
                    <p><strong>Titulaire de la CIN N° :</strong> <?= $emp['cin'] ?></p>
                    <p><strong>Immatriculé(e) à la CNSS sous le N° :</strong> <?= $emp['cnss'] ?></p>
                </div>

                <p>Est employé(e) au sein de notre société en qualité de <strong><?= $emp['poste'] ?></strong> depuis le <strong><?= date('d/m/Y', strtotime($emp['date_embauche'])) ?></strong>.</p>
                
                <p>Cette attestation est délivrée à l'intéressé(e) sur sa demande pour servir et valoir ce que de droit.</p>
            </div>

            <div class="footer-signature">
                <div class="signature-box">
                    <p>Fait à Casablanca, le <?= $date_jour ?></p>
                    <div class="signature-title">Signature et Cachet</div>
                    <?php 
                        $show_signature = !isset($_GET['signed']) || $_GET['signed'] == 1;
                        if (!empty($societe['cachet_path']) && $show_signature): 
                            $cachetSrc = $societe['cachet_path']; 
                            if (strpos($cachetSrc, '../') !== 0) $cachetSrc = '../' . $cachetSrc;
                    ?>
                        <img src="<?= $cachetSrc ?>" alt="Cachet" class="signature-img">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-left">
                <?= !empty($societe['nom']) ? htmlspecialchars($societe['nom']) : 'RED MOTION' ?>
            </div>
            <div class="footer-right">
                <div class="footer-contact">
                    <i class="fas fa-map-marker-alt"></i> <?= !empty($societe['adresse']) ? substr(htmlspecialchars($societe['adresse']), 0, 30) : 'Casablanca' ?>
                </div>
                <div class="footer-contact">
                    <i class="fas fa-phone"></i> <?= !empty($societe['telephone']) ? htmlspecialchars($societe['telephone']) : '+212 6 61 55 33 94' ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
