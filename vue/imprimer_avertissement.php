<?php
include '../config/connexion.php';
include '../model/hr_functions.php';

$id_employe = $_GET['id_employe'] ?? null;
if (!$id_employe) {
    header('Location: retards.php');
    exit;
}

$employe = getEmploye($id_employe);

// Récupérer les infos de la société
$stmt = $connexion->query("SELECT * FROM societe_info LIMIT 1");
$societe = $stmt->fetch(PDO::FETCH_ASSOC);

$date = date('d/m/Y');
$reference = 'AVT-' . date('Ymd') . '-' . str_pad($id_employe, 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avertissement_<?= $employe['nom'] ?>_<?= $employe['prenom'] ?></title>
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
        }

        .employee-card {
            background: var(--light-gray);
            border-radius: 6px;
            padding: 20px;
            border-left: 4px solid var(--secondary-color);
            margin-bottom: 30px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            font-size: 14px;
        }

        .letter-body {
            font-size: 15px;
            text-align: justify;
            color: #2c3e50;
        }

        .letter-subject {
            font-weight: 700;
            font-size: 16px;
            text-decoration: underline;
            margin: 30px 0;
            color: var(--primary-color);
            text-align: center;
        }

        .signature-section {
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
            color: var(--primary-color);
            margin-bottom: 60px;
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

        .footer-left { font-weight: 700; }
        .footer-right { display: flex; gap: 15px; font-size: 11px; }
        .footer-contact { display: flex; align-items: center; gap: 5px; }
        .footer-contact i { color: var(--accent-color); }

        /* IMPRESSION */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0 !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .print-btn { display: none !important; }

            .container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                min-height: 100vh !important;
            }

            .header {
                background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%) !important;
                padding: 15px !important;
                -webkit-print-color-adjust: exact;
            }

            .employee-card {
                background: #f9fafb !important;
                border-left: 4px solid #b91c1c !important;
            }
            
            .footer {
                background: #b91c1c !important;
                color: white !important;
                position: fixed;
                bottom: 0;
                width: 100%;
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
                        <i class="fas fa-exclamation-triangle"></i>
                        AVERTISSEMENT
                    </div>
                    <div class="document-date">
                        Date: <?= $date ?>
                    </div>
                    <div class="document-date">
                        Réf: <?= $reference ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="employee-card">
                <div class="card-title">
                    <i class="fas fa-user-tie"></i> Informations Salarié
                </div>
                <div class="info-grid">
                    <div><strong>Nom & Prénom:</strong> <?= $employe['nom'] . ' ' . $employe['prenom'] ?></div>
                    <div><strong>Matricule:</strong> <?= str_pad($employe['id'], 4, '0', STR_PAD_LEFT) ?></div>
                    <div><strong>Poste:</strong> <?= $employe['poste'] ?></div>
                    <div><strong>Département:</strong> <?= $employe['departement'] ?></div>
                </div>
            </div>

            <div class="letter-subject">OBJET : AVERTISSEMENT POUR RETARDS RÉPÉTÉS</div>

            <div class="letter-body">
                <p>Monsieur/Madame,</p>
                <br>
                <p>Nous avons le regret de constater que vous êtes arrivé(e) en retard à votre poste de travail à plusieurs reprises au cours de ce mois, sans justification valable.</p>
                <br>
                <p>Ces retards répétés perturbent le bon fonctionnement de notre service et sont contraires aux règles de ponctualité en vigueur au sein de notre entreprise. La ponctualité est une qualité essentielle que nous attendons de chacun de nos collaborateurs.</p>
                <br>
                <p>Par la présente, nous vous adressons un <strong>avertissement formel</strong>. Nous vous demandons de prendre immédiatement les dispositions nécessaires pour respecter dorénavant vos horaires de travail.</p>
                <br>
                <p>Nous espérons qu'il ne s'agit que d'un incident isolé et que nous n'aurons pas à prendre de mesures disciplinaires plus sévères à l'avenir.</p>
                <br>
                <p>Veuillez agréer, Monsieur/Madame, l'expression de nos salutations distinguées.</p>
            </div>

            <div class="signature-section">
                <div class="signature-box">
                    <p class="signature-title">La Direction</p>
                    <p style="font-size: 12px; color: #666;">(Cachet et Signature)</p>
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
                <?= !empty($societe['nom']) ? htmlspecialchars($societe['nom']) : 'RED MOTION - Audio Visual et Production Videos' ?>
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
