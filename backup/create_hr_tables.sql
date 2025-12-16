-- Create Departments table
CREATE TABLE IF NOT EXISTS departements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create Job Positions table
CREATE TABLE IF NOT EXISTS postes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    id_departement INT,
    description TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_departement) REFERENCES departements(id) ON DELETE SET NULL
);

-- Create Employees table
CREATE TABLE IF NOT EXISTS employes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    telephone VARCHAR(20),
    date_naissance DATE,
    date_embauche DATE,
    salaire DECIMAL(10, 2),
    id_poste INT,
    statut ENUM('Actif', 'Congé', 'Terminé') DEFAULT 'Actif',
    photo VARCHAR(255),
    adresse TEXT,
    cin VARCHAR(20),
    cnss VARCHAR(50),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_poste) REFERENCES postes(id) ON DELETE SET NULL
);

-- Create Candidates table (Recruitment)
CREATE TABLE IF NOT EXISTS candidats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    telephone VARCHAR(20),
    poste_vise VARCHAR(100),
    cv_path VARCHAR(255),
    statut ENUM('Nouveau', 'Entretien', 'Embauché', 'Rejeté') DEFAULT 'Nouveau',
    date_candidature DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT
);

-- Insert default departments
INSERT INTO departements (nom, description) VALUES 
('Administration', 'Gestion administrative et financière'),
('Cuisine', 'Production et préparation'),
('Vente', 'Service client et vente en boutique'),
('Logistique', 'Livraison et stocks');

-- Insert default positions
INSERT INTO postes (titre, id_departement, description) VALUES 
('Gérant', 1, 'Responsable général'),
('Chef Pâtissier', 2, 'Responsable de la production'),
('Vendeur', 3, 'Accueil et vente'),
('Livreur', 4, 'Livraison des commandes');
