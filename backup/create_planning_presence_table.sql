CREATE TABLE IF NOT EXISTS planning_presence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    date_planning DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_employe) REFERENCES employes(id) ON DELETE CASCADE
);
