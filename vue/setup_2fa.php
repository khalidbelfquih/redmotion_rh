<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

include '../model/GoogleAuthenticator.php';

$ga = new GoogleAuthenticator();
$secret = $ga->createSecret();
$qrCodeUrl = $ga->getQRCodeGoogleUrl('RedMotionRH', $secret);

// Store secret in session to verify later
$_SESSION['2fa_secret_new'] = $secret;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Configuration 2FA - RED MOTION</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        /* Reuse styles from login or dashboard */
        body { font-family: 'DM Sans', sans-serif; background-color: #F8F9FA; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 100%; }
        .qr-code { margin: 1.5rem 0; }
        .btn { background: #E63946; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; }
        .form-control { width: 100%; padding: 10px; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 8px; }
        .back-link { display: block; margin-top: 1rem; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Activer l'authentification 2FA</h2>
        <p>Scannez ce QR code avec Google Authenticator</p>
        
        <div class="qr-code" id="qrcode" style="display: flex; justify-content: center;"></div>
        
        <p>Secret: <strong><?= $secret ?></strong></p>
        
        <form action="../controller/enable_2fa.php" method="post">
            <input type="text" name="code" class="form-control" placeholder="Entrez le code à 6 chiffres" required>
            <button type="submit" name="enable_2fa" class="btn">Activer</button>
        </form>
        
        <a href="dashboard.php" class="back-link">Retour au tableau de bord</a>
    </div>

    <script src="../public/js/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "otpauth://totp/RedMotionRH?secret=<?= $secret ?>&issuer=RedMotionRH",
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
