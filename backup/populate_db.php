<?php
// Script pour enrichir la base de données avec des données de test
// Utilisation : Accéder à ce fichier via le navigateur

require_once 'config/connexion.php';

// Configuration
$nb_clients = 20;
$nb_voitures = 15;
$nb_voitures = 15;
$nb_chauffeurs = 10;
$nb_locations = 30;

// Données de test
$noms = ['Alami', 'Bennani', 'Chraibi', 'Daoudi', 'El Fassi', 'Ghazouani', 'Hassani', 'Idrissi', 'Jettou', 'Kabbaj', 'Lahlou', 'Mernissi', 'Naciri', 'Ouazzani', 'Qadiri', 'Rahmani', 'Slaoui', 'Tazi', 'Wahbi', 'Ziani'];
$prenoms = ['Ahmed', 'Brahim', 'Chadia', 'Driss', 'Fatima', 'Ghita', 'Hassan', 'Imane', 'Jamal', 'Karim', 'Latifa', 'Mehdi', 'Nadia', 'Omar', 'Rachid', 'Samira', 'Tarik', 'Youssef', 'Zineb', 'Amine'];
$marques = [
    'Dacia' => ['Logan', 'Sandero', 'Duster', 'Dokker'],
    'Renault' => ['Clio 4', 'Clio 5', 'Megane', 'Kangoo', 'Captur'],
    'Peugeot' => ['208', '301', '308', '2008', '3008'],
    'Volkswagen' => ['Golf 7', 'Golf 8', 'Polo', 'Touareg', 'Tiguan'],
    'Hyundai' => ['i10', 'i20', 'Accent', 'Tucson', 'Santa Fe'],
    'Citroen' => ['C3', 'C-Elysee', 'Berlingo', 'C4'],
    'Fiat' => ['500', 'Panda', 'Tipo', 'Doblo']
];
$couleurs = ['Blanche', 'Noire', 'Grise', 'Bleue', 'Rouge', 'Argent', 'Beige'];
$carburants = ['Diesel', 'Essence', 'Hybride'];
$villes = ['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Agadir', 'Fes', 'Meknes', 'Oujda'];

// Fonctions utilitaires
function getRandomElement($array) {
    return $array[array_rand($array)];
}

function getRandomDate($startDate, $endDate) {
    $start = strtotime($startDate);
    $end = strtotime($endDate);
    $val = mt_rand($start, $end);
    return date('Y-m-d', $val);
}

function generateMatricule() {
    return mt_rand(1000, 99999) . '-' . chr(rand(65, 68)) . '-' . mt_rand(1, 80);
}

function generatePhone() {
    return '06' . mt_rand(10000000, 99999999);
}

function generateCIN() {
    return getRandomElement(['AB', 'BE', 'BK', 'C', 'D', 'E', 'F']) . mt_rand(10000, 999999);
}

echo "<!DOCTYPE html><html><head><title>Génération de données</title>";
echo "<style>body{font-family:sans-serif;padding:20px;line-height:1.6} .success{color:green} .error{color:red} .info{color:blue}</style>";
echo "</head><body>";
echo "<h1>Génération de données de test</h1>";

try {
    $connexion->beginTransaction();

    // 1. Clients
    echo "<h2>1. Génération des clients ($nb_clients)</h2>";
    $clients_ids = [];
    
    // Récupérer les IDs existants
    $stmt = $connexion->query("SELECT id FROM client");
    $clients_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    for ($i = 0; $i < $nb_clients; $i++) {
        $nom = getRandomElement($noms);
        $prenom = getRandomElement($prenoms);
        $telephone = generatePhone();
        $email = strtolower($prenom . '.' . $nom . mt_rand(1, 99) . '@email.com');
        $adresse = mt_rand(1, 999) . ' Bd ' . getRandomElement(['Mohammed V', 'Hassan II', 'Zerktouni', 'Massira']) . ', ' . getRandomElement($villes);
        $cin = generateCIN();
        $permis = mt_rand(10000, 99999) . '/B';
        $date_naissance = getRandomDate('1970-01-01', '2000-12-31');

        $sql = "INSERT INTO client (nom, prenom, telephone, email, adresse, cin, permis_conduire, date_naissance) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([$nom, $prenom, $telephone, $email, $adresse, $cin, $permis, $date_naissance]);
        $clients_ids[] = $connexion->lastInsertId();
    }
    echo "<p class='success'>Clients générés avec succès.</p>";

    // 2. Voitures
    echo "<h2>2. Génération des voitures ($nb_voitures)</h2>";
    $voitures_ids = [];
    
    // Récupérer les IDs existants
    $stmt = $connexion->query("SELECT id FROM voiture");
    $voitures_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    for ($i = 0; $i < $nb_voitures; $i++) {
        $marque = array_rand($marques);
        $modele = getRandomElement($marques[$marque]);
        $matricule = generateMatricule();
        $couleur = getRandomElement($couleurs);
        $carburant = getRandomElement($carburants);
        $annee = mt_rand(2018, 2024);
        $prix_jour = mt_rand(20, 60) * 10; // 200 à 600
        $kilometrage = mt_rand(10000, 150000);
        $etat = getRandomElement(['Disponible', 'Disponible', 'Disponible', 'Loué', 'En maintenance']);
        
        // Dates pour alertes
        $date_mc = getRandomDate(($annee-1).'-01-01', $annee.'-12-31');
        
        // Générer des dates d'assurance/visite proches ou expirées pour tester les alertes
        $rand_alert = mt_rand(1, 10);
        if ($rand_alert == 1) {
            // Expiré
            $date_ass = getRandomDate('2023-01-01', date('Y-m-d', strtotime('-1 day')));
        } elseif ($rand_alert == 2) {
            // Expire bientôt
            $date_ass = getRandomDate(date('Y-m-d'), date('Y-m-d', strtotime('+20 days')));
        } else {
            // Valide
            $date_ass = getRandomDate(date('Y-m-d', strtotime('+2 months')), date('Y-m-d', strtotime('+1 year')));
        }
        
        $date_visite = getRandomDate($date_ass, date('Y-m-d', strtotime('+1 year')));

        $sql = "INSERT INTO voiture (matricule, marque, modele, couleur, carburant, annee, kilometrage, prix_jour, etat, date_mise_en_circulation, date_assurance, date_visite_technique, transmission) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([$matricule, $marque, $modele, $couleur, $carburant, $annee, $kilometrage, $prix_jour, $etat, $date_mc, $date_ass, $date_visite, getRandomElement(['Manuelle', 'Automatique'])]);
        $voitures_ids[] = $connexion->lastInsertId();
    }
    echo "<p class='success'>Voitures générées avec succès.</p>";

    // 3. Chauffeurs
    echo "<h2>3. Génération des chauffeurs ($nb_chauffeurs)</h2>";
    $chauffeurs_ids = [];
    
    // Récupérer les IDs existants
    $stmt = $connexion->query("SELECT id FROM chauffeur");
    $chauffeurs_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    for ($i = 0; $i < $nb_chauffeurs; $i++) {
        $nom = getRandomElement($noms);
        $prenom = getRandomElement($prenoms);
        $cnie = generateCIN();
        $telephone = generatePhone();
        $telephone_urgence = generatePhone();
        $email = strtolower($prenom . '.' . $nom . mt_rand(1, 99) . '@email.com');
        $adresse = mt_rand(1, 999) . ' Rue ' . getRandomElement(['Atlas', 'Rif', 'Souss', 'Oum Rabia']) . ', ' . getRandomElement($villes);
        $date_naissance = getRandomDate('1980-01-01', '1995-12-31');
        $date_embauche = getRandomDate('2020-01-01', date('Y-m-d'));
        $numero_permis = mt_rand(100000, 999999) . '/B';
        $date_expiration_permis = getRandomDate(date('Y-m-d', strtotime('+1 year')), date('Y-m-d', strtotime('+5 years')));
        $categorie_permis = getRandomElement(['B', 'C', 'D']);
        $salaire_base = mt_rand(3000, 8000);
        $statut = getRandomElement(['Actif', 'Actif', 'Actif', 'En congé', 'Maladie']);
        $observations = "Chauffeur généré automatiquement.";

        $sql = "INSERT INTO chauffeur (nom, prenom, cnie, telephone, telephone_urgence, email, adresse, date_naissance, date_embauche, numero_permis, date_expiration_permis, categorie_permis, salaire_base, statut, observations) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([$nom, $prenom, $cnie, $telephone, $telephone_urgence, $email, $adresse, $date_naissance, $date_embauche, $numero_permis, $date_expiration_permis, $categorie_permis, $salaire_base, $statut, $observations]);
        $chauffeurs_ids[] = $connexion->lastInsertId();
    }
    echo "<p class='success'>Chauffeurs générés avec succès.</p>";

    // 4. Locations
    echo "<h2>4. Génération des locations ($nb_locations)</h2>";
    
    if (empty($clients_ids) || empty($voitures_ids)) {
        throw new Exception("Pas assez de clients ou voitures pour générer des locations.");
    }

    for ($i = 0; $i < $nb_locations; $i++) {
        $id_client = getRandomElement($clients_ids);
        $id_voiture = getRandomElement($voitures_ids);
        
        // Date de location dans les 6 derniers mois
        $date_debut = getRandomDate(date('Y-m-d', strtotime('-6 months')), date('Y-m-d'));
        $duree = mt_rand(2, 15);
        $date_fin = date('Y-m-d', strtotime($date_debut . " + $duree days"));
        
        // Prix
        $stmt = $connexion->prepare("SELECT prix_jour FROM voiture WHERE id = ?");
        $stmt->execute([$id_voiture]);
        $prix_jour = $stmt->fetchColumn();
        $prix_total = $prix_jour * $duree;
        
        // État et Paiement
        $today = date('Y-m-d');
        if ($date_fin < $today) {
            $etat = 'Terminée';
            $date_retour = $date_fin;
            $montant_paye = $prix_total; // Payé en totalité
        } elseif ($date_debut > $today) {
            $etat = 'Réservée';
            $date_retour = null;
            $montant_paye = mt_rand(0, $prix_total / 2); // Avance
        } else {
            $etat = 'En cours';
            $date_retour = null;
            $montant_paye = mt_rand($prix_total / 2, $prix_total);
        }

        $sql = "INSERT INTO location (id_client, id_voiture, date_location, date_debut, date_fin_prevue, date_retour_reelle, prix_total, montant_paye, etat) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([$id_client, $id_voiture, $date_debut, $date_debut, $date_fin, $date_retour, $prix_total, $montant_paye, $etat]);
        $id_location = $connexion->lastInsertId();
        
        // Générer un paiement
        if ($montant_paye > 0) {
            $sql_paiement = "INSERT INTO paiement_location (id_location, montant, date_paiement, mode_paiement) VALUES (?, ?, ?, ?)";
            $stmt_paiement = $connexion->prepare($sql_paiement);
            $stmt_paiement->execute([$id_location, $montant_paye, $date_debut, getRandomElement(['Espèces', 'Virement', 'Chèque', 'Carte Bancaire'])]);
        }
    }
    echo "<p class='success'>Locations et paiements générés avec succès.</p>";

    // 5. Utilisateurs (si pas d'admin)
    $stmt = $connexion->query("SELECT COUNT(*) FROM utilisateur WHERE role = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $password = md5('admin123');
        $sql = "INSERT INTO utilisateur (nom, prenom, email, password, role) VALUES ('Admin', 'System', 'admin@elkhaldi.com', '$password', 'admin')";
        $connexion->exec($sql);
        echo "<p class='info'>Compte admin créé (admin@elkhaldi.com / admin123).</p>";
    }

    $connexion->commit();
    echo "<h2>Terminé !</h2>";
    echo "<p>La base de données a été enrichie.</p>";
    echo "<a href='vue/dashboard.php'>Aller au tableau de bord</a>";

} catch (Exception $e) {
    $connexion->rollBack();
    echo "<h2 class='error'>Erreur</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
}
echo "</body></html>";
?>
