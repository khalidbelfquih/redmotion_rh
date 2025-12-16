-- Script de migration pour le système de location de voitures

-- Désactiver les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS = 0;

-- Suppression des tables liées à l'optique (nettoyage)
DROP TABLE IF EXISTS detail_vente;
DROP TABLE IF EXISTS detail_commande;
DROP TABLE IF EXISTS detail_paiement;
DROP TABLE IF EXISTS prescription;
DROP TABLE IF EXISTS garantie;
DROP TABLE IF EXISTS promotion;
DROP TABLE IF EXISTS categorie_article;

-- Correction de la table client (Ajout PK et Auto_Increment si manquant)
-- On essaie de modifier la colonne id pour qu'elle soit PK et Auto_Increment
-- Note: Si des doublons d'ID existent, cela pourrait échouer. On suppose que les données sont propres ou on vide la table si nécessaire.
-- Pour être sûr, on peut supprimer les clés primaires existantes (si erreur, on ignore)
-- ALTER TABLE client DROP PRIMARY KEY; 
ALTER TABLE client MODIFY id INT AUTO_INCREMENT PRIMARY KEY;

-- Ajout des colonnes spécifiques location à la table client
ALTER TABLE client ADD COLUMN IF NOT EXISTS permis_conduire VARCHAR(50);
ALTER TABLE client ADD COLUMN IF NOT EXISTS date_naissance DATE;
ALTER TABLE client ADD COLUMN IF NOT EXISTS cin VARCHAR(20);

-- Renommer et adapter la table article en voiture
DROP TABLE IF EXISTS article;
CREATE TABLE IF NOT EXISTS voiture (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(20) NOT NULL UNIQUE,
    marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    couleur VARCHAR(30),
    carburant ENUM('Essence', 'Diesel', 'Hybride', 'Electrique') DEFAULT 'Diesel',
    annee INT,
    kilometrage INT DEFAULT 0,
    prix_jour DECIMAL(10, 2) NOT NULL,
    etat ENUM('Disponible', 'Loué', 'En maintenance', 'Indisponible') DEFAULT 'Disponible',
    image VARCHAR(255),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Renommer et adapter la table vente en location
DROP TABLE IF EXISTS vente;
CREATE TABLE IF NOT EXISTS location (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_voiture INT NOT NULL,
    date_location DATE NOT NULL,
    date_debut DATE NOT NULL,
    date_fin_prevue DATE NOT NULL,
    date_retour_reelle DATE,
    prix_total DECIMAL(10, 2),
    montant_paye DECIMAL(10, 2) DEFAULT 0,
    etat ENUM('En cours', 'Terminée', 'Annulée', 'Réservée') DEFAULT 'En cours',
    observation TEXT,
    user_createur INT,
    FOREIGN KEY (id_client) REFERENCES client(id),
    FOREIGN KEY (id_voiture) REFERENCES voiture(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table pour les paiements de location
CREATE TABLE IF NOT EXISTS paiement_location (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_location INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
    mode_paiement VARCHAR(50) DEFAULT 'Espèces',
    user_id INT,
    FOREIGN KEY (id_location) REFERENCES location(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Réactiver les vérifications
SET FOREIGN_KEY_CHECKS = 1;

-- Insertion de quelques données de test
INSERT INTO voiture (matricule, marque, modele, couleur, carburant, annee, prix_jour, etat) VALUES 
('12345-A-1', 'Dacia', 'Logan', 'Blanche', 'Diesel', 2023, 250.00, 'Disponible'),
('67890-B-6', 'Renault', 'Clio 5', 'Noire', 'Diesel', 2024, 300.00, 'Disponible'),
('11223-D-26', 'Peugeot', '208', 'Grise', 'Essence', 2022, 280.00, 'En maintenance');
