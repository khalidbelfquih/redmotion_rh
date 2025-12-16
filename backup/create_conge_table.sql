CREATE TABLE IF NOT EXISTS conges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    type_conge ENUM('Annuel', 'Maladie', 'Sans Solde', 'Maternité', 'Autre') NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    motif TEXT,
    statut ENUM('En attente', 'Approuvé', 'Refusé') DEFAULT 'En attente',
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP,
    commentaire_admin TEXT,
    FOREIGN KEY (id_employe) REFERENCES employes(id) ON DELETE CASCADE
);
