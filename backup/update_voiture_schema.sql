-- Script de mise à jour de la table voiture
-- Ajout des champs demandés par l'utilisateur

ALTER TABLE voiture ADD COLUMN IF NOT EXISTS type_papier VARCHAR(50);
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS date_achat DATE;
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS prix_achat DECIMAL(10, 2);
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS transmission ENUM('Manuelle', 'Automatique') DEFAULT 'Manuelle';
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS description TEXT;
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS deleted TINYINT(1) DEFAULT 0;
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS date_mise_en_circulation DATE;
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS date_assurance DATE;
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS date_visite_technique DATE;
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS carte_grise VARCHAR(100);
ALTER TABLE voiture ADD COLUMN IF NOT EXISTS papier VARCHAR(255);

-- Mise à jour de la colonne image si elle n'existe pas (déjà présente normalement)
-- ALTER TABLE voiture ADD COLUMN IF NOT EXISTS image VARCHAR(255);
