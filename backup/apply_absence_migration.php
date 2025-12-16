<?php
require_once 'config/connexion.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS suivi_absence (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_employe INT NOT NULL,
        date_debut DATE NOT NULL,
        date_fin DATE NOT NULL,
        type_absence ENUM('Maladie', 'Congé Sans Solde', 'Absence Injustifiée', 'Autre') NOT NULL,
        motif TEXT,
        justifie TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_employe) REFERENCES employes(id) ON DELETE CASCADE
    )";
    
    $connexion->exec($sql);
    echo "Table 'suivi_absence' created successfully or already exists.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
