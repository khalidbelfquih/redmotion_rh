<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Connexion | Red Motion</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --bg-white: #FFFFFF;
            --bg-light: #F8F9FA;
            --red-primary: #E63946;
            --red-dark: #B71C1C;
            --red-light: #FFCDD2;
            --text-dark: #1A1A1A;
            --text-medium: #4A4A4A;
            --text-light: #8C8C8C;
            --shadow-glow: 0 8px 32px rgba(230, 57, 70, 0.25);
            --gradient-primary: linear-gradient(135deg, var(--red-primary) 0%, var(--red-dark) 100%);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            filter: blur(80px);
            opacity: 0.6;
            animation: float 20s infinite ease-in-out;
        }

        .shape-1 {
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, var(--red-light) 0%, transparent 70%);
            animation-delay: 0s;
        }

        .shape-2 {
            bottom: -10%;
            right: -10%;
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, rgba(230, 57, 70, 0.15) 0%, transparent 70%);
            animation-delay: -5s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, 50px) rotate(10deg); }
            66% { transform: translate(-20px, 20px) rotate(-5deg); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }

        .icon-container {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .floating-icon {
            position: absolute;
            color: var(--red-primary);
            opacity: 0.1;
            animation: floatIcon 15s linear infinite;
        }

        @keyframes floatIcon {
            0% { transform: translateY(110vh) rotate(0deg) scale(0.8); opacity: 0; }
            10% { opacity: 0.15; }
            90% { opacity: 0.15; }
            100% { transform: translateY(-20vh) rotate(360deg) scale(1.2); opacity: 0; }
        }

        .login-card {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 3.5rem;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.05),
                        0 0 0 1px rgba(255, 255, 255, 0.5);
            width: 100%;
            max-width: 480px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.08),
                        0 0 0 1px rgba(255, 255, 255, 0.6);
        }

        .logo-container {
            margin-bottom: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 20px;
            box-shadow: var(--shadow-glow);
            color: white;
            font-size: 40px;
            transform: rotate(-5deg);
        }

        .brand-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 1rem;
            color: var(--text-medium);
            margin-bottom: 2.5rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-left: 4px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 1.25rem;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 16px 16px 16px 50px;
            border: 2px solid transparent; /* Prepare for border transition */
            background: #fff;
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            color: var(--text-dark);
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }

        .form-control::placeholder {
            color: #ccc;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--red-primary);
            box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.1);
        }

        .form-control:focus + i {
            color: var(--red-primary);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1.5rem;
            letter-spacing: 0.5px;
            box-shadow: var(--shadow-glow);
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(230, 57, 70, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .alert-danger {
            background: #FEF2F2;
            color: var(--red-dark);
            border: 1px solid #FECACA;
        }

        .alert-success {
            background: #F0FDF4;
            color: #15803D;
            border: 1px solid #BBF7D0;
        }

        .footer-text {
            margin-top: 2.5rem;
            font-size: 0.8rem;
            color: var(--text-light);
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 2rem;
                border-radius: 20px;
            }
            .shape-1, .shape-2 {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="bg-animation">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
    </div>
    
    <!-- Floating Icons Container -->
    <div class="icon-container" id="iconContainer"></div>

    <div class="login-card">
        <div class="logo-container">
            <i class='bx bxs-user-circle'></i>
        </div>
        
        <?php if (isset($_GET['step']) && $_GET['step'] == '2fa'): ?>
            <!-- 2FA Form -->
            <h2 style="margin-bottom: 1rem; color: var(--text-dark);">Vérification 2FA</h2>
            <p style="margin-bottom: 2rem; color: var(--text-medium);">Entrez le code de votre application</p>

            <form method="post" action="../controller/verify_2fa.php">
                <?php if (!empty($_SESSION['message']['text'])): ?>
                    <div class="alert alert-<?= isset($_SESSION['message']['type']) && $_SESSION['message']['type'] == 'danger' ? 'danger' : 'success' ?>">
                        <i class='bx <?= isset($_SESSION['message']['type']) && $_SESSION['message']['type'] == 'danger' ? 'bx-error-circle' : 'bx-check-circle' ?>'></i>
                        <?= $_SESSION['message']['text'] ?>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Code à 6 chiffres</label>
                    <div class="input-wrapper">
                        <input type="text" name="code" class="form-control" placeholder="123456" required pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" autofocus>
                        <i class='bx bx-key'></i>
                    </div>
                </div>

                <button type="submit" name="verify_2fa" class="btn-login">
                    Vérifier
                </button>
                
                <a href="login.php" class="footer-text" style="display:block; margin-top:1rem; text-decoration:none;">Annuler</a>
            </form>

        <?php else: ?>
            <!-- Standard Login Form -->
            <h1 class="brand-title">RED MOTION</h1>
            <p class="brand-subtitle">Bienvenue sur votre espace RH</p>

            <form method="post" action="../controller/authentification.php">
                <?php if (!empty($_SESSION['message']['text'])): ?>
                    <div class="alert alert-<?= isset($_SESSION['message']['type']) && $_SESSION['message']['type'] == 'danger' ? 'danger' : 'success' ?>">
                        <i class='bx <?= isset($_SESSION['message']['type']) && $_SESSION['message']['type'] == 'danger' ? 'bx-error-circle' : 'bx-check-circle' ?>'></i>
                        <?= $_SESSION['message']['text'] ?>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Email Professionnel</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" class="form-control" placeholder="exemple@redmotion.ma" required>
                        <i class='bx bx-envelope'></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        <i class='bx bx-lock-alt'></i>
                    </div>
                </div>

                <button type="submit" name="login" class="btn-login">
                    Se connecter
                </button>
            </form>
        <?php endif; ?>

        <div class="footer-text">
            &copy; <?= date('Y') ?> RED MOTION. Tous droits réservés.
        </div>
    </div>

    <script>
        // Generate floating icons
        const icons = [
            'bx-camera', 
            'bx-camera-movie', 
            'bx-video', 
            'bx-film', 
            'bx-aperture',
            'bx-video-recording',
            'bx-movie-play',
            'bx-image'
        ];
        const container = document.getElementById('iconContainer');

        function createIcon() {
            const icon = document.createElement('i');
            const randomIcon = icons[Math.floor(Math.random() * icons.length)];
            icon.className = `bx ${randomIcon} floating-icon`;
            
            // Random positioning and sizing
            const left = Math.random() * 100;
            const size = Math.random() * 2 + 1.5; // 1.5rem to 3.5rem
            const duration = Math.random() * 15 + 15; // 15s to 30s
            const delay = Math.random() * -30; // Negative delay to start mid-animation

            icon.style.left = `${left}%`;
            icon.style.fontSize = `${size}rem`;
            icon.style.animationDuration = `${duration}s`;
            icon.style.animationDelay = `${delay}s`;

            // Randomize color slightly
            if (Math.random() > 0.7) {
                icon.style.color = 'var(--text-dark)';
                icon.style.opacity = '0.05';
            }

            container.appendChild(icon);
        }

        // Create initial batch of icons
        for (let i = 0; i < 20; i++) {
            createIcon();
        }
    </script>
</body>
</html>