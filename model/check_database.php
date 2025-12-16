<?php
// Script de vérification de la base de données
// Placez ce fichier à la racine du projet et exécutez-le depuis le navigateur

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Vérification de la configuration de la base de données</h1>";

// Paramètres de connexion (copier depuis votre fichier connexion.php)
$nom_serveur = "localhost";
$nom_base_de_donne = "gestion_stock_dclic";
$utilisateur = "root";
$motpass = "";

echo "<h2>Paramètres de connexion</h2>";
echo "<p>Serveur: $nom_serveur</p>";
echo "<p>Base de données: $nom_base_de_donne</p>";
echo "<p>Utilisateur: $utilisateur</p>";
echo "<p>Mot de passe: [masqué]</p>";

// Tester la connexion
echo "<h2>Test de connexion à la base de données</h2>";
try {
    $connexion = new PDO("mysql:host=$nom_serveur;dbname=$nom_base_de_donne", $utilisateur, $motpass);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green;'>✓ Connexion à la base de données réussie!</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Erreur de connexion: " . $e->getMessage() . "</p>";
    die();
}

// Vérifier si la table utilisateur existe
echo "<h2>Vérification de la table 'utilisateur'</h2>";
try {
    $sql = "SHOW TABLES LIKE 'utilisateur'";
    $result = $connexion->query($sql);
    
    if ($result->rowCount() > 0) {
        echo "<p style='color:green;'>✓ La table 'utilisateur' existe.</p>";
        
        // Vérifier la structure de la table
        echo "<h3>Structure de la table 'utilisateur'</h3>";
        $sql = "DESCRIBE utilisateur";
        $result = $connexion->query($sql);
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Vérifier les données
        echo "<h3>Données de la table 'utilisateur'</h3>";
        $sql = "SELECT id, nom, prenom, email, role, password, date_creation FROM utilisateur";
        $result = $connexion->query($sql);
        
        if ($result->rowCount() > 0) {
            echo "<p style='color:green;'>✓ La table 'utilisateur' contient " . $result->rowCount() . " enregistrement(s).</p>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Rôle</th><th>Hash du mot de passe</th><th>Date de création</th></tr>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . (strlen($value) > 30 ? substr($value, 0, 30) . "..." : $value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color:red;'>✗ La table 'utilisateur' est vide. Veuillez exécuter le script SQL pour créer des utilisateurs.</p>";
        }
        
    } else {
        echo "<p style='color:red;'>✗ La table 'utilisateur' n'existe pas. Veuillez exécuter le script SQL.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Erreur: " . $e->getMessage() . "</p>";
}

// Tester l'authentification avec des identifiants connus
echo "<h2>Test d'authentification</h2>";
try {
    $email = "admin@dclic.com";
    $password = "admin123";
    
    echo "<p>Test avec les identifiants suivants:</p>";
    echo "<p>Email: $email</p>";
    echo "<p>Mot de passe: $password</p>";
    
    $sql = "SELECT * FROM utilisateur WHERE email = ?";
    $req = $connexion->prepare($sql);
    $req->execute(array($email));
    
    $user = $req->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p style='color:green;'>✓ Utilisateur trouvé dans la base de données.</p>";
        echo "<p>Hash du mot de passe enregistré: " . $user['password'] . "</p>";
        
        // Générer un hash pour le mot de passe de test
        $hash_test = password_hash($password, PASSWORD_DEFAULT);
        echo "<p>Nouveau hash généré pour 'admin123': $hash_test</p>";
        
        // Vérifier le mot de passe
        if (password_verify($password, $user['password'])) {
            echo "<p style='color:green;'>✓ Vérification du mot de passe réussie!</p>";
        } else {
            echo "<p style='color:red;'>✗ Échec de la vérification du mot de passe.</p>";
            echo "<p>Insertion d'un nouvel utilisateur avec un mot de passe correctement hashé...</p>";
            
            // Essayer d'insérer un nouvel utilisateur d'administration
            $sql = "INSERT INTO utilisateur (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?)";
            $req = $connexion->prepare($sql);
            $req->execute(array(
                'Nouvel', 
                'Admin', 
                'nouvel.admin@dclic.com', 
                password_hash('admin123', PASSWORD_DEFAULT), 
                'admin'
            ));
            
            if ($req->rowCount() > 0) {
                echo "<p style='color:green;'>✓ Nouvel utilisateur ajouté avec succès.</p>";
                echo "<p>Email: nouvel.admin@dclic.com</p>";
                echo "<p>Mot de passe: admin123</p>";
            } else {
                echo "<p style='color:red;'>✗ Échec de l'ajout d'un nouvel utilisateur.</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>✗ Utilisateur non trouvé dans la base de données.</p>";
        
        // Essayer d'insérer un utilisateur d'administration
        echo "<p>Tentative d'insertion d'un utilisateur administrateur...</p>";
        
        $sql = "INSERT INTO utilisateur (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $req = $connexion->prepare($sql);
        $req->execute(array(
            'Admin', 
            'System', 
            'admin@dclic.com', 
            password_hash('admin123', PASSWORD_DEFAULT), 
            'admin'
        ));
        
        if ($req->rowCount() > 0) {
            echo "<p style='color:green;'>✓ Utilisateur admin ajouté avec succès.</p>";
        } else {
            echo "<p style='color:red;'>✗ Échec de l'ajout de l'utilisateur admin.</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Erreur: " . $e->getMessage() . "</p>";
}

echo "<h2>Conclusion</h2>";
echo "<p>Si des erreurs ont été détectées, veuillez les corriger avant de continuer.</p>";
echo "<p>Pour utiliser la solution de connexion simplifiée en attendant, utilisez le fichier <b>login_simple.php</b>.</p>";
?>