CREATE TABLE IF NOT EXISTS primes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    motif VARCHAR(255) NOT NULL,
    date_prime DATE NOT NULL,
    FOREIGN KEY (id_employe) REFERENCES employes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS deductions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    motif VARCHAR(255) NOT NULL,
    date_deduction DATE NOT NULL,
    FOREIGN KEY (id_employe) REFERENCES employes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS paiements_salaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    mois INT NOT NULL,
    annee INT NOT NULL,
    salaire_base DECIMAL(10, 2) NOT NULL,
    total_primes DECIMAL(10, 2) DEFAULT 0,
    total_deductions DECIMAL(10, 2) DEFAULT 0,
    salaire_net DECIMAL(10, 2) NOT NULL,
    date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50) DEFAULT 'Payé',
    FOREIGN KEY (id_employe) REFERENCES employes(id) ON DELETE CASCADE
);
