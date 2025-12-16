<!DOCTYPE html>
<html lang="fr" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <title>Connexion - D-CLIC Stock</title>
    <link rel="stylesheet" href="../public/css/style.css" />
    <!-- Boxicons CDN Link -->
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 30px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            color: #0a2558;
            margin-bottom: 10px;
        }
        
        .login-form input {
            margin-bottom: 15px;
        }
        
        .login-form button {
            width: 100%;
            height: 40px;
            margin-top: 15px;
            background-color: #0a2558;
        }
        
        .login-form button:hover {
            background-color: #081d45;
        }
        
        .alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h2>D-CLIC Stock</h2>
            <p>Système de Gestion de Stock</p>
        </div>
        
        <form class="login-form" action="../model/authentification.php" method="post">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Veuillez saisir votre email" required>
            
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" placeholder="Veuillez saisir votre mot de passe" required>
            
            <button type="submit">Se connecter</button>
            
            <?php
            session_start();
            if (!empty($_SESSION['message']['text'])) {
            ?>
                <div class="alert <?= $_SESSION['message']['type'] ?>">
                    <?= $_SESSION['message']['text'] ?>
                </div>
            <?php
                unset($_SESSION['message']);
            }
            ?>
        </form>
    </div>
</body>
</html>