<?php
include 'config/connexion.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS documents_employe (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_employe INT NOT NULL,
        nom_fichier VARCHAR(255) NOT NULL,
        chemin_fichier VARCHAR(255) NOT NULL,
        date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_employe) REFERENCES employes(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $connexion->exec($sql);
    echo "Table 'documents_employe' created successfully.";
} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
