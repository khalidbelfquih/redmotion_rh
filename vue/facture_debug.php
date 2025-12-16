<?php
// Activer l'affichage de toutes les erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Message de base pour confirmer que le script s'exécute
echo "<h2>Test de la facture - Vérification du script</h2>";

// Vérifier si on a reçu un ID
if (isset($_GET['id'])) {
    echo "<p>ID reçu: " . $_GET['id'] . "</p>";
} else {
    echo "<p>Aucun ID n'a été passé dans l'URL. Utilisez ?id=X dans l'URL.</p>";
}

// Vérifier si les fichiers requis sont disponibles
echo "<h3>Vérification des fichiers requis:</h3>";
echo "<ul>";

// Vérifier entete.php
if (file_exists('entete.php')) {
    echo "<li>entete.php existe ✓</li>";
} else {
    echo "<li style='color:red'>entete.php n'existe pas ✗</li>";
}

// Essayer d'inclure la connexion à la base de données sans rien exécuter
try {
    echo "<li>Tentative d'inclusion de entete.php...</li>";
    ob_start();
    include 'entete.php';
    ob_end_clean();
    echo "<li>entete.php a été inclus avec succès ✓</li>";
    
    // Vérifier si $connexion existe après inclusion
    if (isset($connexion)) {
        echo "<li>Objet de connexion à la base de données trouvé ✓</li>";
    } else {
        echo "<li style='color:red'>Objet de connexion à la base de données non trouvé ✗</li>";
    }
    
} catch (Exception $e) {
    echo "<li style='color:red'>Erreur lors de l'inclusion de entete.php: " . $e->getMessage() . " ✗</li>";
}

echo "</ul>";

// Si on a un ID et que l'inclusion a réussi, essayer une requête SQL de test
if (isset($_GET['id']) && isset($connexion)) {
    echo "<h3>Test de la requête SQL:</h3>";
    
    try {
        // Requête SQL simple pour obtenir les données de base de la vente
        $id_vente = $_GET['id'];
        $sql = "SELECT v.id, v.date_vente, c.nom, c.prenom 
                FROM vente v
                JOIN client c ON v.id_client = c.id
                WHERE v.id = ?";
        
        $req = $connexion->prepare($sql);
        $req->execute([$id_vente]);
        $vente_test = $req->fetch(PDO::FETCH_ASSOC);
        
        if ($vente_test) {
            echo "<div style='background-color: #d4edda; padding: 10px; border-radius: 5px;'>";
            echo "<p><strong>Données de base récupérées avec succès:</strong></p>";
            echo "<ul>";
            echo "<li>ID Vente: " . $vente_test['id'] . "</li>";
            echo "<li>Date: " . $vente_test['date_vente'] . "</li>";
            echo "<li>Client: " . $vente_test['nom'] . " " . $vente_test['prenom'] . "</li>";
            echo "</ul>";
            echo "</div>";
            
            echo "<p>Votre connexion à la base de données fonctionne correctement.</p>";
            
            // Afficher un lien pour tester la vraie facture
            echo "<a href='facture_vision_ka.php?id={$id_vente}' style='display: inline-block; padding: 10px 20px; background-color: #0a2558; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px;'>
                Tester la vraie facture
            </a>";
            
        } else {
            echo "<div style='background-color: #f8d7da; padding: 10px; border-radius: 5px;'>";
            echo "<p><strong>Aucune vente trouvée avec l'ID " . $id_vente . ".</strong></p>";
            echo "<p>Vérifiez que l'ID existe dans votre base de données.</p>";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "<div style='background-color: #f8d7da; padding: 10px; border-radius: 5px;'>";
        echo "<p><strong>Erreur SQL:</strong> " . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

// Vérifier la structure de la table vente
if (isset($connexion)) {
    echo "<h3>Structure de la table vente:</h3>";
    
    try {
        $sql = "SHOW COLUMNS FROM vente";
        $req = $connexion->query($sql);
        $colonnes = $req->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th></tr>";
        
        foreach ($colonnes as $colonne) {
            echo "<tr>";
            echo "<td>" . $colonne['Field'] . "</td>";
            echo "<td>" . $colonne['Type'] . "</td>";
            echo "<td>" . $colonne['Null'] . "</td>";
            echo "<td>" . $colonne['Key'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
    } catch (PDOException $e) {
        echo "<div style='background-color: #f8d7da; padding: 10px; border-radius: 5px;'>";
        echo "<p><strong>Erreur lors de la récupération de la structure de la table:</strong> " . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

// Afficher des instructions de débogage
echo "<h3>Étapes suivantes pour résoudre le problème:</h3>";
echo "<ol>";
echo "<li>Vérifiez si les erreurs sont affichées ci-dessus</li>";
echo "<li>Assurez-vous que l'ID de vente que vous utilisez existe dans la base de données</li>";
echo "<li>Vérifiez si la connexion à la base de données fonctionne</li>";
echo "<li>Si ce script s'affiche correctement mais que la facture ne s'affiche pas, le problème pourrait être dans le code HTML ou CSS de la facture</li>";
echo "</ol>";

echo "<p>Pour obtenir de l'aide supplémentaire, créez un fichier simplifié comme celui-ci et ajoutez progressivement le code de la facture.</p>";