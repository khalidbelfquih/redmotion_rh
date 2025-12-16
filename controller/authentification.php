<?php
include '../config/connexion.php';
include '../model/function.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        try {
            // Requête préparée pour éviter les injections SQL
            $sql = "SELECT * FROM utilisateur WHERE email = ?";
            $req = $connexion->prepare($sql);
            $req->execute(array($email));
            
            $user = $req->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Hachage du mot de passe saisi (MD5 pour compatibilité)
                $password_hashed = md5($password);
                
                // Vérifier le mot de passe
                if ($password_hashed === $user['password']) {
                    // Vérifier si 2FA est activé
                    if (!empty($user['google_2fa_secret'])) {
                        $_SESSION['2fa_pending_user'] = [
                            'id' => $user['id'],
                            'nom' => $user['nom'],
                            'prenom' => $user['prenom'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                            'secret' => $user['google_2fa_secret']
                        ];
                        // REDIRECT TO LOGIN PAGE WITH STEP=2FA
                        header('Location: ../vue/login.php?step=2fa');
                        exit();
                    }

                    // Authentification réussie (sans 2FA)
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'nom' => $user['nom'],
                        'prenom' => $user['prenom'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    
                    // Redirection vers dashboard
                    header('Location: ../vue/dashboard.php');
                    exit();
                } else {
                    // Mot de passe incorrect
                    $_SESSION['message']['text'] = "Email ou mot de passe incorrect";
                    $_SESSION['message']['type'] = "danger";
                }
            } else {
                // Utilisateur non trouvé
                $_SESSION['message']['text'] = "Email ou mot de passe incorrect";
                $_SESSION['message']['type'] = "danger";
            }
        } catch (PDOException $e) {
            // Erreur de base de données
            $_SESSION['message']['text'] = "Erreur de base de données: " . $e->getMessage();
            $_SESSION['message']['type'] = "danger";
        }
    } else {
        // Champs vides
        $_SESSION['message']['text'] = "Veuillez remplir tous les champs";
        $_SESSION['message']['type'] = "danger";
    }
}

// Redirection vers page de connexion si échec ou accès direct
header('Location: ../vue/login.php');
exit();
?>