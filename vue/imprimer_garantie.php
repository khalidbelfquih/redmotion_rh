<?php
include '../model/connexion.php';
require_once '../model/ajoutGarantie.php';

// Récupérer l'ID de la garantie
$id_garantie = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_garantie) {
    // Rediriger si pas d'ID
    header('Location: garanties.php');
    exit;
}

// Récupérer les détails de la garantie
$sql = "SELECT g.*, v.date_vente, v.id_client, v.prix, a.nom_article, a.marque, a.modele, 
               c.nom, c.prenom, c.telephone, c.email, c.adresse
        FROM garantie g
        JOIN vente v ON g.id_vente = v.id
        JOIN article a ON v.id_article = a.id
        JOIN client c ON v.id_client = c.id
        WHERE g.id = ?";
$req = $connexion->prepare($sql);
$req->execute([$id_garantie]);
$garantie = $req->fetch();

if (!$garantie) {
    // Rediriger si garantie non trouvée
    header('Location: garanties.php');
    exit;
}

// Calculer la durée de la garantie en années et mois
$date_debut = new DateTime($garantie['date_debut']);
$date_fin = new DateTime($garantie['date_fin']);
$interval = $date_debut->diff($date_fin);
$duree = '';
if ($interval->y > 0) {
    $duree .= $interval->y . ' an' . ($interval->y > 1 ? 's' : '');
}
if ($interval->m > 0) {
    $duree .= ($duree ? ' et ' : '') . $interval->m . ' mois';
}

// Récupérer les informations de la société
$stmt_soc = $connexion->query("SELECT * FROM societe_info LIMIT 1");
$societe = $stmt_soc->fetch(PDO::FETCH_ASSOC);

// Valeurs par défaut si pas de données en base
if (!$societe) {
    $societe = [
        'nom' => 'Visonka-Homi',
        'adresse' => '15 Av. Al Abtal, Rabat',
        'telephone' => '+212 6 50 78 96 02',
        'logo_path' => '../public/images/log.png'
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat de Garantie - GAR-<?= str_pad($garantie['id'], 6, '0', STR_PAD_LEFT) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 5px;
            color: #333;
            background-color: white;
        }
        .container {
            width: 210mm;
            min-height: 280mm;
            margin: 0 auto;
            border: 2px solid #0a2558;
            border-radius: 10px;
            padding: 10mm;
            position: relative;
            background-color: white;
            box-sizing: border-box;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%230a2558' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0a2558;
            padding-bottom: 10px;
            margin-bottom: 10px;
            position: relative;
            min-height: 90px; /* Espace pour le logo */
            background: linear-gradient(to right, rgba(10, 37, 88, 0.05), rgba(10, 37, 88, 0.01), rgba(10, 37, 88, 0.05));
            border-radius: 8px 8px 0 0;
        }
        .logo {
            position: absolute;
            top: 0;
            left: 0;
            max-height: 80px;
            max-width: 280px;
        }
        .title {
            color: #0a2558;
            font-size: 24px;
            margin-bottom: 3px;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(10, 37, 88, 0.2);
        }
        .subtitle {
            color: #666;
            font-size: 13px;
        }
        .section-title {
            color: #0a2558;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            margin: 10px 0 6px 0;
            font-size: 16px;
        }
        .row {
            display: flex;
            margin-bottom: 8px;
        }
        .col {
            flex: 1;
        }
        .label {
            font-weight: bold;
            margin-right: 3px;
            color: #0a2558;
        }
        p {
            margin: 3px 0;
        }
        .footer {
            padding-top: 8px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ddd;
            background: linear-gradient(to right, rgba(10, 37, 88, 0.05), rgba(10, 37, 88, 0.01), rgba(10, 37, 88, 0.05));
            border-radius: 0 0 8px 8px;
            margin-top: 8px;
        }
        .footer p {
            margin: 2px 0;
        }
        .stamp {
            text-align: right;
            margin-top: 8px;
            margin-bottom: 8px;
        }
        .stamp-box {
            display: inline-block;
            border: 1px dashed #0a2558;
            padding: 8px 20px;
            text-align: center;
            background-color: rgba(10, 37, 88, 0.02);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .stamp-box p {
            margin: 2px 0;
        }
        .conditions {
            background-color: #f9f9f9;
            padding: 8px;
            border-radius: 5px;
            margin-top: 8px;
            font-size: 11px;
            border-left: 3px solid #0a2558;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .conditions h4 {
            margin: 0 0 3px 0;
            color: #0a2558;
            font-size: 12px;
        }
        .conditions p {
            margin: 3px 0;
        }
        .info-box {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #0a2558;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: background-color 0.3s;
            z-index: 1000;
        }
        .print-button:hover {
            background-color: #0d306e;
        }
        .corner-decoration {
            position: absolute;
            width: 60px;
            height: 60px;
            background-size: contain;
            background-repeat: no-repeat;
            z-index: 0;
            opacity: 0.1;
        }
        .top-left {
            top: 10px;
            left: 10px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath d='M0 0 L100 0 L100 100 Z' fill='%230a2558'/%3E%3C/svg%3E");
        }
        .top-right {
            top: 10px;
            right: 10px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath d='M0 0 L100 0 L0 100 Z' fill='%230a2558'/%3E%3C/svg%3E");
        }
        .bottom-left {
            bottom: 10px;
            left: 10px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath d='M0 100 L100 100 L100 0 Z' fill='%230a2558'/%3E%3C/svg%3E");
        }
        .bottom-right {
            bottom: 10px;
            right: 10px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath d='M0 100 L100 100 L0 0 Z' fill='%230a2558'/%3E%3C/svg%3E");
        }
        
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            html, body {
                width: 100%;
                height: 100%;
                margin: 0 !important;
                padding: 0 !important;
                background-color: white;
            }
            .print-button {
                display: none !important;
            }
            body {
                padding: 0 !important;
            }
            .container {
                width: 210mm;
                height: 297mm;
                margin: 0 !important;
                padding: 8mm !important;
                border: none !important;
                box-shadow: none;
                page-break-inside: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                box-sizing: border-box !important;
            }
            
            /* Conserver l'arrière-plan des éléments */
            .conditions, .info-box {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            /* Suppression des URL générées automatiquement par les navigateurs */
            a:after {
                content: none !important;
            }
            a[href^="http"]:after {
                content: none !important;
            }
            /* Conserver les couleurs des bordures */
            .stamp-box {
                border: 1px dashed #0a2558 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .header {
                border-bottom: 2px solid #0a2558 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .section-title {
                border-bottom: 1px solid #ddd !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .footer {
                border-top: 1px solid #ddd !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .corner-decoration {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .conditions {
                border-left: 3px solid #0a2558 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Imprimer</button>
    
    <div class="container">
        <!-- Décorations de coin -->
        <div class="corner-decoration top-left"></div>
        <div class="corner-decoration top-right"></div>
        <div class="corner-decoration bottom-left"></div>
        <div class="corner-decoration bottom-right"></div>
        
        <!-- En-tête du certificat -->
        <div class="header">
            <!-- Logo de la société -->
            <img src="<?= !empty($societe['logo_path']) ? $societe['logo_path'] : '../public/images/log.png' ?>" alt="Logo de la société" class="logo">
            <div class="title" style="margin-top: 15px;">CERTIFICAT DE GARANTIE</div>
            <div class="subtitle">Référence: GAR-<?= str_pad($garantie['id'], 6, '0', STR_PAD_LEFT) ?></div>
        </div>
        
        <!-- Informations client -->
        <h3 class="section-title">Informations client</h3>
        <div class="info-box">
            <div class="row">
                <div class="col">
                    <p><span class="label">Nom:</span> <?= $garantie['nom'] . ' ' . $garantie['prenom'] ?></p>
                    <p><span class="label">Adresse:</span> <?= $garantie['adresse'] ?></p>
                </div>
                <div class="col">
                    <p><span class="label">Téléphone:</span> <?= $garantie['telephone'] ?></p>
                    <?php if (!empty($garantie['email'])): ?>
                    <p><span class="label">Email:</span> <?= $garantie['email'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Informations produit -->
        <h3 class="section-title">Produit concerné</h3>
        <div class="info-box">
            <div class="row">
                <div class="col">
                    <p><span class="label">Article:</span> <?= $garantie['nom_article'] ?></p>
                    <?php if (!empty($garantie['marque'])): ?>
                    <p><span class="label">Marque:</span> <?= $garantie['marque'] ?></p>
                    <?php endif; ?>
                    <?php if (!empty($garantie['modele'])): ?>
                    <p><span class="label">Modèle:</span> <?= $garantie['modele'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="col">
                    <p><span class="label">Prix:</span> <?= number_format($garantie['prix'], 0, ',', ' ') ?> DH</p>
                    <p><span class="label">Date d'achat:</span> <?= date('d/m/Y', strtotime($garantie['date_vente'])) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Détails de la garantie -->
        <h3 class="section-title">Détails de la garantie</h3>
        <div class="info-box">
            <div class="row">
                <div class="col">
                    <p>
                        <span class="label">Type de garantie:</span> 
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
                                echo 'Autre';
                        }
                        ?>
                    </p>
                    <p><span class="label">Durée:</span> <?= $duree ?></p>
                </div>
                <div class="col">
                    <p><span class="label">Date de début:</span> <?= date('d/m/Y', strtotime($garantie['date_debut'])) ?></p>
                    <p><span class="label">Date de fin:</span> <?= date('d/m/Y', strtotime($garantie['date_fin'])) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Conditions de la garantie avec hauteur réduite -->
        <?php if (!empty($garantie['conditions'])): ?>
        <div class="conditions">
            <h4>Conditions:</h4>
            <p><?= nl2br(htmlspecialchars($garantie['conditions'])) ?></p>
        </div>
        <?php else: ?>
        <div class="conditions">
            <h4>Conditions générales:</h4>
            <p>1. Cette garantie couvre les défauts de fabrication et de matériaux.</p>
            <p>2. Les dommages dus à une utilisation inappropriée, à un accident ou à l'usure normale ne sont pas couverts.</p>
            <p>3. Toute modification ou réparation effectuée par une personne non autorisée annule cette garantie.</p>
            <p>4. Pour bénéficier de la garantie, le client doit présenter ce certificat et la preuve d'achat.</p>
        </div>
        <?php endif; ?>
        
        <!-- Notes - Affichées seulement si présentes et avec hauteur réduite -->
        <?php if (!empty($garantie['notes'])): ?>
        <div class="conditions">
            <h4>Notes:</h4>
            <p><?= nl2br(htmlspecialchars($garantie['notes'])) ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Tampon et signature avec hauteur réduite -->
        <div class="stamp">
            <div class="stamp-box">
                <p>Date et signature</p>
                <p>_______________________</p>
            </div>
        </div>
        
        <!-- Pied de page plus compact -->
        <div class="footer">
            <p>Ce certificat de garantie doit être conservé et présenté pour toute demande de service.</p>
            <p><?= htmlspecialchars($societe['nom']) ?> | Adresse: <?= htmlspecialchars($societe['adresse']) ?> | Téléphone: <?= htmlspecialchars($societe['telephone']) ?></p>
            <p>Document généré le <?= date('d/m/Y') ?></p>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fonction pour imprimer correctement
        window.printCertificat = function() {
            // Masquer temporairement les éléments non imprimables
            const printButton = document.querySelector('.print-button');
            const originalDisplay = printButton.style.display;
            printButton.style.display = 'none';
            
            // Imprimer
            window.print();
            
            // Restaurer les éléments masqués
            setTimeout(function() {
                printButton.style.display = originalDisplay;
            }, 1000);
        };
        
        // Associer l'action au bouton d'impression
        document.querySelector('.print-button').addEventListener('click', function(e) {
            e.preventDefault();
            printCertificat();
        });
    });
    </script>
</body>
</html>