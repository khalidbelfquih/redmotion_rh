<?php
session_start();
// If needed, include function.php or connection.php to verify password
// Actually we need to verify the password against the logged in user's password.
// We'll send the request to a controller.

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Verrouillée</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --red-primary: #E63946;
            --bg-light: #F8F9FA;
            --text-dark: #1A1A1A;
            --text-muted: #ADB5BD;
        }
        
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .lock-container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 90%;
            animation: fadeIn 0.5s ease-out;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--red-primary);
            color: white;
            font-size: 32px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
        }

        h2 {
            margin: 0 0 5px;
            color: var(--text-dark);
        }

        p {
            color: var(--text-muted);
            margin: 0 0 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s;
        }

        input[type="password"]:focus {
            border-color: var(--red-primary);
            box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
        }

        .btn-unlock {
            background: var(--red-primary);
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-unlock:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
        }

        .error-msg {
            color: var(--red-primary);
            font-size: 12px;
            margin-top: 10px;
            display: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="lock-container">
        <div class="user-avatar">
            <?= strtoupper(substr($_SESSION['user']['prenom'], 0, 1) . substr($_SESSION['user']['nom'], 0, 1)) ?>
        </div>
        <h2><?= $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'] ?></h2>
        <p>Application verrouillée</p>

        <form action="../controller/unlock_app.php" method="POST">
            <div class="form-group">
                <input type="password" name="password" placeholder="Votre mot de passe" required autofocus autocomplete="off">
            </div>
            <button type="submit" class="btn-unlock">
                <i class='bx bx-lock-open-alt'></i> Déverrouiller
            </button>
            <?php if (isset($_SESSION['error_unlock'])): ?>
                <div class="error-msg" style="display:block;">
                    <?= $_SESSION['error_unlock'] ?>
                </div>
                <?php unset($_SESSION['error_unlock']); ?>
            <?php endif; ?>
        </form>
    </div>

</body>
</html>
