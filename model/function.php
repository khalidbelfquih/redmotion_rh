<?php
include_once __DIR__ . '/../config/connexion.php';

// Modifiez la fonction getArticle dans function.php pour inclure les nouveaux champs

function getPDO() {
    return $GLOBALS['connexion'];
}

function getArticle($id = null, $searchDATA = array(), $limit = null, $offset = null)
{
    $pagination = "";
    if (!empty($limit) && (!empty($offset) || $offset == 0)) {
        $pagination = " LIMIT $limit OFFSET $offset";
    }
    if (!empty($id)) {
        $sql = "SELECT a.id AS id, a.id_categorie, a.nom_article, c.libelle_categorie, a.quantite, a.prix_unitaire, 
        a.date_fabrication, a.date_expiration, a.images, a.marque, a.modele, a.reference, a.couleur, a.matiere, 
        a.forme, a.diametre, a.emplacement
        FROM article AS a, categorie_article AS c WHERE a.id=? AND c.id=a.id_categorie";

        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute(array($id));

        return $req->fetch();
    } elseif (!empty($searchDATA)) {
        $search = "";
        extract($searchDATA);
        if (!empty($nom_article)) $search .= " AND a.nom_article LIKE '%$nom_article%' ";
        if (!empty($marque)) $search .= " AND a.marque LIKE '%$marque%' ";
        if (!empty($reference)) $search .= " AND a.reference LIKE '%$reference%' "; // Recherche par référence
        if (!empty($id_categorie)) $search .= " AND a.id_categorie = $id_categorie ";
        if (!empty($quantite)) $search .= " AND a.quantite >= $quantite ";
        if (!empty($prix_unitaire)) $search .= " AND a.prix_unitaire <= $prix_unitaire ";
        if (!empty($date_fabrication)) $search .= " AND DATE(a.date_fabrication) = '$date_fabrication' ";
        if (!empty($date_expiration)) $search .= " AND DATE(a.date_expiration) = '$date_expiration' ";

        $sql = "SELECT a.id AS id, a.id_categorie, a.nom_article, c.libelle_categorie, a.quantite, a.prix_unitaire, 
        a.date_fabrication, a.date_expiration, a.images, a.marque, a.modele, a.reference, a.couleur, a.matiere, 
        a.forme, a.diametre, a.emplacement
        FROM article AS a, categorie_article AS c WHERE c.id=a.id_categorie $search $pagination";

        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();

        return $req->fetchAll();
    } else {
        $sql = "SELECT a.id AS id, a.id_categorie, a.nom_article, c.libelle_categorie, a.quantite, a.prix_unitaire, 
        a.date_fabrication, a.date_expiration, a.images, a.marque, a.modele, a.reference, a.couleur, a.matiere, 
        a.forme, a.diametre, a.emplacement
        FROM article AS a, categorie_article AS c WHERE c.id=a.id_categorie $pagination";

        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute();
        
        return $req->fetchAll();
    }
}
function countArticle($searchDATA = array())
{
   if (!empty($searchDATA)) {
        $search = "";
        extract($searchDATA);
        if (!empty($nom_article)) $search .= " AND a.nom_article LIKE '%$nom_article%' ";
        if (!empty($marque)) $search .= " AND a.marque LIKE '%$marque%' ";
        if (!empty($reference)) $search .= " AND a.reference LIKE '%$reference%' "; // Ajout de la recherche par référence
        if (!empty($id_categorie)) $search .= " AND a.id_categorie = $id_categorie ";
        if (!empty($quantite)) $search .= " AND a.quantite = $quantite ";
        if (!empty($prix_unitaire)) $search .= " AND a.prix_unitaire = $prix_unitaire ";
        if (!empty($date_fabrication)) $search .= " AND DATE(a.date_fabrication) = '$date_fabrication' ";
        if (!empty($date_expiration)) $search .= " AND DATE(a.date_expiration) = '$date_expiration' ";

        $sql = "SELECT COUNT(*) AS total FROM article AS a, categorie_article AS c WHERE c.id=a.id_categorie $search";

        $req = $GLOBALS['connexion']->prepare($sql);

        $req->execute();

        return $req->fetch();
    } else {
        $sql = "SELECT COUNT(*) AS total 
        FROM article AS a, categorie_article AS c WHERE c.id=a.id_categorie";
        $req = $GLOBALS['connexion']->prepare($sql);

        $req->execute();
        return $req->fetch();
    }
}

// Modifiez cette fonction dans votre fichier function.php

function getClient($id = null)
{
    if (!empty($id)) {
        $sql = "SELECT * FROM client WHERE id=?";

        $req = $GLOBALS['connexion']->prepare($sql);

        $req->execute(array($id));

        return $req->fetch();
    } else {
        $sql = "SELECT * FROM client ORDER BY nom, prenom";

        $req = $GLOBALS['connexion']->prepare($sql);

        $req->execute();

        return $req->fetchAll();
    }
}



function getAllCommande()
{
    $sql = "SELECT COUNT(*) AS nbre FROM commande";

    $req = $GLOBALS['connexion']->prepare($sql);

    $req->execute();

    return $req->fetch();
}

function getAllVente()
{
    $sql = "SELECT COUNT(*) AS nbre FROM vente WHERE etat=?";

    $req = $GLOBALS['connexion']->prepare($sql);

    $req->execute(array(1));

    return $req->fetch();
}

function getAllArticle()
{
    $sql = "SELECT COUNT(*) AS nbre FROM article";

    $req = $GLOBALS['connexion']->prepare($sql);

    $req->execute();

    return $req->fetch();
}

function getCA()
{
    $sql = "SELECT SUM(prix) AS prix FROM vente";

    $req = $GLOBALS['connexion']->prepare($sql);

    $req->execute();

    return $req->fetch();
}

function getLastVente()
{

    $sql = "SELECT nom_article, nom, prenom, v.quantite, prix, date_vente, v.id, a.id AS idArticle
        FROM client AS c, vente AS v, article AS a WHERE v.id_article=a.id AND v.id_client=c.id AND etat=? 
        ORDER BY date_vente DESC LIMIT 10";

    $req = $GLOBALS['connexion']->prepare($sql);

    $req->execute(array(1));

    return $req->fetchAll();
}

function getMostVente()
{

    $sql = "SELECT nom_article, SUM(prix) AS prix
        FROM client AS c, vente AS v, article AS a WHERE v.id_article=a.id AND v.id_client=c.id AND etat=? 
        GROUP BY a.id
        ORDER BY SUM(prix) DESC LIMIT 10";

    $req = $GLOBALS['connexion']->prepare($sql);

    $req->execute(array(1));

    return $req->fetchAll();
}

function getCategorie($id = null)
{
    if (!empty($id)) {
        $sql = "SELECT * FROM categorie_article WHERE id=?";

        $req = $GLOBALS['connexion']->prepare($sql);

        $req->execute(array($id));

        return $req->fetch();
    } else {
        $sql = "SELECT * FROM categorie_article";

        $req = $GLOBALS['connexion']->prepare($sql);

        $req->execute();

        return $req->fetchAll();
    }
}

/**
 * Récupère un ou tous les utilisateurs
 * @param int|null $id ID de l'utilisateur à récupérer (null pour tous)
 * @return array Liste des utilisateurs ou informations d'un utilisateur
 */
function getUtilisateur($id = null) {
    global $connexion;
    
    if ($id !== null) {
        $sql = "SELECT * FROM utilisateur WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute(array($id));
        return $req->fetch();
    } else {
        $sql = "SELECT * FROM utilisateur ORDER BY nom, prenom";
        $req = $connexion->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }
}




function getHistoriqueVentesArticle($id_article) {
    global $connexion;
    
    $sql = "SELECT lv.quantite, lv.prix_total as prix, v.date_vente, c.nom, c.prenom, v.id as id_vente
            FROM ligne_vente lv
            JOIN vente v ON lv.id_vente = v.id
            JOIN client c ON v.id_client = c.id
            WHERE lv.id_article = ?
            ORDER BY v.date_vente DESC";
    
    $req = $connexion->prepare($sql);
    $req->execute([$id_article]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getHistoriqueCommandesArticle($id_article) {
    if (!empty($id_article)) {
        $sql = "SELECT co.*, f.nom, f.prenom 
                FROM commande co 
                JOIN fournisseur f ON co.id_fournisseur = f.id 
                WHERE co.id_article = ? 
                ORDER BY co.date_commande DESC";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute(array($id_article));
        return $req->fetchAll();
    }
    return null;
}



function getNiveauStock() {
    // Obtenir les articles en stock faible (seuil défini à 5 unités)
    $sql = "SELECT a.id, a.nom_article, a.marque, a.modele, a.quantite, a.prix_unitaire, c.libelle_categorie 
            FROM article a 
            JOIN categorie_article c ON a.id_categorie = c.id 
            WHERE a.quantite <= 5 
            ORDER BY a.quantite ASC";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    return $req->fetchAll();
}

function getArticlesExpirants() {
    // Obtenir les articles qui expirent dans les 30 jours
    $sql = "SELECT a.id, a.nom_article, a.marque, a.modele, a.quantite, a.date_expiration, c.libelle_categorie 
            FROM article a 
            JOIN categorie_article c ON a.id_categorie = c.id 
            WHERE a.date_expiration <= DATE_ADD(NOW(), INTERVAL 30 DAY) 
            AND a.date_expiration >= NOW() 
            ORDER BY a.date_expiration ASC";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    return $req->fetchAll();
}

function getTopVentesPeriode($debut, $fin) {
    // Obtenir les articles les plus vendus sur une période
    $sql = "SELECT a.id, a.nom_article, a.marque, a.modele, SUM(v.quantite) as total_vendu, SUM(v.prix) as chiffre_affaire 
            FROM vente v 
            JOIN article a ON v.id_article = a.id 
            WHERE v.etat = 1 AND v.date_vente BETWEEN ? AND ? 
            GROUP BY a.id 
            ORDER BY total_vendu DESC 
            LIMIT 10";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute(array($debut, $fin));
    return $req->fetchAll();
}

function getCAParCategorie($debut, $fin) {
    // Obtenir le chiffre d'affaires par catégorie sur une période
    $sql = "SELECT c.id, c.libelle_categorie, SUM(v.prix) as chiffre_affaire 
            FROM vente v 
            JOIN article a ON v.id_article = a.id 
            JOIN categorie_article c ON a.id_categorie = c.id 
            WHERE v.etat = 1 AND v.date_vente BETWEEN ? AND ? 
            GROUP BY c.id 
            ORDER BY chiffre_affaire DESC";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute(array($debut, $fin));
    return $req->fetchAll();
}




// Fonctions pour les statistiques optiques






// Fonction utilitaire pour formater les prix
function formatMontant($montant) {
    return number_format($montant, 0, ',', ' ') . ' DH';
}


//////////////////////////


// Fonction pour récupérer les informations d'une vente
function getVente() {
    $sql = "SELECT id, date_vente, prix, nom, prenom, telephone, montant_total, montant_paye, reste_a_payer,
            (qte_articles_supplementaires + quantite) AS nb_articles
            FROM vue_ventes_details
            ORDER BY date_vente DESC, id DESC";
    
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    return $req->fetchAll();
}

/**
 * Récupère l'historique des achats d'un client
 * 
 * @param int $id_client L'ID du client
 * @return array Liste des achats du client
 */
function getHistoriqueAchatsClient($id_client) {
    if (empty($id_client)) {
        return array();
    }
    
    // Requête de base pour récupérer les ventes du client
    $sql = "SELECT v.id, v.id_article, v.quantite, v.prix, v.date_vente, v.etat,
                   a.nom_article, a.marque, a.modele,
                   c.id AS id_categorie, c.libelle_categorie
            FROM vente v
            JOIN article a ON v.id_article = a.id
            JOIN categorie_article c ON a.id_categorie = c.id
            WHERE v.id_client = ? AND v.etat = 1
            ORDER BY v.date_vente DESC";
    
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute(array($id_client));
    $ventes = $req->fetchAll(PDO::FETCH_ASSOC);
    
    // Si des détails optiques sont disponibles, les ajouter
    if (!empty($ventes)) {
        foreach ($ventes as $key => $vente) {
            // Vérifier séparément pour chaque vente si des détails optiques existent
            $sql_optique = "SELECT id, date_livraison FROM vente_optique WHERE id_vente = ?";
            $req_optique = $GLOBALS['connexion']->prepare($sql_optique);
            $req_optique->execute(array($vente['id']));
            $optique = $req_optique->fetch(PDO::FETCH_ASSOC);
            
            // Ajouter les informations optiques si elles existent
            if ($optique) {
                $ventes[$key]['id_vente_optique'] = $optique['id'];
                $ventes[$key]['date_livraison'] = $optique['date_livraison'];
            } else {
                $ventes[$key]['id_vente_optique'] = null;
                $ventes[$key]['date_livraison'] = null;
            }
        }
    }
    
    return $ventes;
}


/**
 * Récupère les détails d'une vente optique par son ID de vente
 * 
 * @param int $id_vente ID de la vente
 * @return array|bool Détails de la vente optique ou false si non trouvée
 */


/**
 * Récupère toutes les ventes optiques
 * 
 * @return array Liste des ventes optiques avec informations complémentaires
 */


/**
 * Récupère les ventes optiques en attente de livraison
 * 
 * @param int $limit Nombre maximal de résultats à retourner
 * @return array Liste des ventes optiques en attente
 */




/**
 * Récupère les détails d'une vente optique à partir de l'ID de vente
 * 
 * @param int $id_vente ID de la vente
 * @return array|null Données de la vente optique ou null si non trouvée
 */



/* 
 * Mise à jour des fonctions pour prendre en compte les nouveaux champs 
 * Vous devez ajouter ces fonctions à votre fichier fonction.php existant
 */

/**
 * Récupère les fournisseurs avec les nouveaux champs
 * @param int|null $id ID du fournisseur (optionnel)
 * @return array Résultats de la requête
 */
function getFournisseur($id = null) {
    require 'connexion.php';
    
    $sql = "SELECT * FROM fournisseur";
    
    if($id != null) {
        $sql .= " WHERE id = :id";
        $req = $connexion->prepare($sql);
        $req->bindParam(':id', $id);
    } else {
        $req = $connexion->prepare($sql);
    }
    
    $req->execute();
    
    if($id != null) {
        return $req->fetch();
    } else {
        return $req->fetchAll();
    }
}

/**
 * Ajoute un fournisseur avec les nouveaux champs
 * @param array $data Données du fournisseur
 * @return bool Résultat de l'opération
 */
function ajoutFournisseur($data) {
    require 'connexion.php';
    
    $sql = "INSERT INTO fournisseur(nom, prenom, telephone, adresse, societe, ice, email, site_web) 
            VALUES(:nom, :prenom, :telephone, :adresse, :societe, :ice, :email, :site_web)";
    
    $req = $connexion->prepare($sql);
    
    $req->bindParam(':nom', $data['nom']);
    $req->bindParam(':prenom', $data['prenom']);
    $req->bindParam(':telephone', $data['telephone']);
    $req->bindParam(':adresse', $data['adresse']);
    $req->bindParam(':societe', $data['societe']);
    $req->bindParam(':ice', $data['ice']);
    $req->bindParam(':email', $data['email']);
    $req->bindParam(':site_web', $data['site_web']);
    
    return $req->execute();
}

/**
 * Modifie un fournisseur avec les nouveaux champs
 * @param array $data Données du fournisseur
 * @return bool Résultat de l'opération
 */
function modifFournisseur($data) {
    require 'connexion.php';
    
    $sql = "UPDATE fournisseur 
            SET nom = :nom, prenom = :prenom, telephone = :telephone, adresse = :adresse, 
                societe = :societe, ice = :ice, email = :email, site_web = :site_web
            WHERE id = :id";
    
    $req = $connexion->prepare($sql);
    
    $req->bindParam(':nom', $data['nom']);
    $req->bindParam(':prenom', $data['prenom']);
    $req->bindParam(':telephone', $data['telephone']);
    $req->bindParam(':adresse', $data['adresse']);
    $req->bindParam(':societe', $data['societe']);
    $req->bindParam(':ice', $data['ice']);
    $req->bindParam(':email', $data['email']);
    $req->bindParam(':site_web', $data['site_web']);
    $req->bindParam(':id', $data['id']);
    
    return $req->execute();
}

/**
 * Récupère les commandes avec les nouveaux champs
 * @param int|null $id ID de la commande (optionnel)
 * @return array Résultats de la requête
 */
function getCommande($id = null) {
    require 'connexion.php';
    
    $sql = "SELECT c.*, c.id as id, a.id as idArticle, a.nom_article, f.id as idFournisseur, 
                   f.nom, f.prenom, f.societe, f.ice 
            FROM commande c
            INNER JOIN article a ON c.id_article = a.id 
            INNER JOIN fournisseur f ON c.id_fournisseur = f.id";
    
    if($id != null) {
        $sql .= " WHERE c.id = :id";
        $req = $connexion->prepare($sql);
        $req->bindParam(':id', $id);
    } else {
        $sql .= " ORDER BY c.date_commande DESC";
        $req = $connexion->prepare($sql);
    }
    
    $req->execute();
    
    if($id != null) {
        return $req->fetch();
    } else {
        return $req->fetchAll();
    }
}

/**
 * Ajoute une commande avec les nouveaux champs
 * @param array $data Données de la commande
 * @return bool Résultat de l'opération
 */
function ajoutCommande($data) {
    require 'connexion.php';
    
    // Mettre à jour la quantité dans la table article
    $sqlUpdate = "UPDATE article SET quantite = quantite + :quantite WHERE id = :id_article";
    $reqUpdate = $connexion->prepare($sqlUpdate);
    $reqUpdate->bindParam(':quantite', $data['quantite']);
    $reqUpdate->bindParam(':id_article', $data['id_article']);
    $reqUpdate->execute();
    
    // Calculer le prix unitaire
    $prix_unitaire = $data['quantite'] > 0 ? $data['prix'] / $data['quantite'] : 0;
    
    $sql = "INSERT INTO commande(id_article, id_fournisseur, quantite, prix, date_commande, 
                                 numero_commande, statut, date_livraison, prix_unitaire, notes) 
            VALUES(:id_article, :id_fournisseur, :quantite, :prix, NOW(), 
                  :numero_commande, :statut, :date_livraison, :prix_unitaire, :notes)";
    
    $req = $connexion->prepare($sql);
    
    $req->bindParam(':id_article', $data['id_article']);
    $req->bindParam(':id_fournisseur', $data['id_fournisseur']);
    $req->bindParam(':quantite', $data['quantite']);
    $req->bindParam(':prix', $data['prix']);
    $req->bindParam(':numero_commande', $data['numero_commande']);
    $req->bindParam(':statut', $data['statut']);
    $req->bindParam(':date_livraison', $data['date_livraison']);
    $req->bindParam(':prix_unitaire', $prix_unitaire);
    $req->bindParam(':notes', $data['notes']);
    
    return $req->execute();
}

/**
 * Modifie une commande avec les nouveaux champs
 * @param array $data Données de la commande
 * @return bool Résultat de l'opération
 */
function modifCommande($data) {
    require 'connexion.php';
    
    // Récupérer l'ancienne commande pour ajuster le stock
    $sqlSelect = "SELECT id_article, quantite FROM commande WHERE id = :id";
    $reqSelect = $connexion->prepare($sqlSelect);
    $reqSelect->bindParam(':id', $data['id']);
    $reqSelect->execute();
    $oldCommande = $reqSelect->fetch();
    
    // Calculer la différence de quantité pour mettre à jour le stock
    $diffQuantite = $data['quantite'] - $oldCommande['quantite'];
    
    // Mettre à jour le stock pour l'article si l'ID a changé ou la quantité
    if($oldCommande['id_article'] != $data['id_article']) {
        // Restaurer le stock de l'ancien article
        $sqlUpdateOld = "UPDATE article SET quantite = quantite - :quantite WHERE id = :id_article";
        $reqUpdateOld = $connexion->prepare($sqlUpdateOld);
        $reqUpdateOld->bindParam(':quantite', $oldCommande['quantite']);
        $reqUpdateOld->bindParam(':id_article', $oldCommande['id_article']);
        $reqUpdateOld->execute();
        
        // Mettre à jour le stock du nouvel article
        $sqlUpdateNew = "UPDATE article SET quantite = quantite + :quantite WHERE id = :id_article";
        $reqUpdateNew = $connexion->prepare($sqlUpdateNew);
        $reqUpdateNew->bindParam(':quantite', $data['quantite']);
        $reqUpdateNew->bindParam(':id_article', $data['id_article']);
        $reqUpdateNew->execute();
    } else if($diffQuantite != 0) {
        // Ajuster le stock pour le même article si la quantité a changé
        $sqlUpdateSame = "UPDATE article SET quantite = quantite + :diff WHERE id = :id_article";
        $reqUpdateSame = $connexion->prepare($sqlUpdateSame);
        $reqUpdateSame->bindParam(':diff', $diffQuantite);
        $reqUpdateSame->bindParam(':id_article', $data['id_article']);
        $reqUpdateSame->execute();
    }
    
    // Calculer le prix unitaire
    $prix_unitaire = $data['quantite'] > 0 ? $data['prix'] / $data['quantite'] : 0;
    
    $sql = "UPDATE commande 
            SET id_article = :id_article, id_fournisseur = :id_fournisseur, 
                quantite = :quantite, prix = :prix, 
                numero_commande = :numero_commande, statut = :statut, 
                date_livraison = :date_livraison, prix_unitaire = :prix_unitaire, 
                notes = :notes
            WHERE id = :id";
    
    $req = $connexion->prepare($sql);
    
    $req->bindParam(':id_article', $data['id_article']);
    $req->bindParam(':id_fournisseur', $data['id_fournisseur']);
    $req->bindParam(':quantite', $data['quantite']);
    $req->bindParam(':prix', $data['prix']);
    $req->bindParam(':numero_commande', $data['numero_commande']);
    $req->bindParam(':statut', $data['statut']);
    $req->bindParam(':date_livraison', $data['date_livraison']);
    $req->bindParam(':prix_unitaire', $prix_unitaire);
    $req->bindParam(':notes', $data['notes']);
    $req->bindParam(':id', $data['id']);
    
    return $req->execute();
}

/**
 * Annule une commande et met à jour le stock
 * @param int $idCommande ID de la commande
 * @param int $idArticle ID de l'article
 * @param int $quantite Quantité à annuler
 * @return bool Résultat de l'opération
 */
function annuleCommande($idCommande, $idArticle, $quantite) {
    require 'connexion.php';
    
    // Mettre à jour le stock en retirant la quantité
    $sqlUpdate = "UPDATE article SET quantite = quantite - :quantite WHERE id = :id_article";
    $reqUpdate = $connexion->prepare($sqlUpdate);
    $reqUpdate->bindParam(':quantite', $quantite);
    $reqUpdate->bindParam(':id_article', $idArticle);
    $reqUpdate->execute();
    
    // Marquer la commande comme annulée
    $sqlDelete = "UPDATE commande SET statut = 'annulee' WHERE id = :id";
    $reqDelete = $connexion->prepare($sqlDelete);
    $reqDelete->bindParam(':id', $idCommande);
    
    return $reqDelete->execute();
}

/**
 * Valide une commande comme livrée et met à jour le stock
 * @param int $idCommande ID de la commande
 * @return bool Résultat de l'opération
 */
function livreCommande($idCommande) {
    require 'connexion.php';
    
    // Marquer la commande comme livrée
    $sql = "UPDATE commande SET statut = 'livree' WHERE id = :id";
    $req = $connexion->prepare($sql);
    $req->bindParam(':id', $idCommande);
    
    return $req->execute();
}
function getArticlesStats() {
    global $connexion;
    
    try {
        // Initialiser les statistiques
        $stats = [
            'valeur_totale' => 0,
            'faible_stock' => 0
        ];
        
        // Calculer la valeur totale du stock (prix * quantité pour chaque article)
        // Assurez-vous que 'article' est bien le nom de votre table. Si ce n'est pas le cas, remplacez-le par le nom correct.
        $sql_valeur = "SELECT SUM(prix_unitaire * quantite) as valeur_totale FROM article";
        $req_valeur = $connexion->query($sql_valeur);
        $resultat_valeur = $req_valeur->fetch(PDO::FETCH_ASSOC);
        
        if ($resultat_valeur && isset($resultat_valeur['valeur_totale'])) {
            $stats['valeur_totale'] = $resultat_valeur['valeur_totale'];
        }
        
        // Compter les articles en faible stock (quantité < 5 par exemple)
        $seuil_faible_stock = 1; // Définir le seuil pour "faible stock"
        $sql_faible_stock = "SELECT COUNT(*) as faible_stock FROM article WHERE quantite < :seuil";
        $req_faible_stock = $connexion->prepare($sql_faible_stock);
        $req_faible_stock->bindParam(':seuil', $seuil_faible_stock, PDO::PARAM_INT);
        $req_faible_stock->execute();
        $resultat_faible_stock = $req_faible_stock->fetch(PDO::FETCH_ASSOC);
        
        if ($resultat_faible_stock && isset($resultat_faible_stock['faible_stock'])) {
            $stats['faible_stock'] = $resultat_faible_stock['faible_stock'];
        }
        
        return $stats;
        
    } catch (PDOException $e) {
        // En cas d'erreur, retourner un tableau vide ou gérer l'erreur comme souhaité
        error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
        return [
            'valeur_totale' => 0,
            'faible_stock' => 0
        ];
    }
}






