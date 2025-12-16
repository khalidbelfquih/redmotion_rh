<?php
require_once '../config/connexion.php';

// Create employe_documents table
$sql = "CREATE TABLE IF NOT EXISTS employe_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    fichier VARCHAR(255) NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_employe) REFERENCES employes(id) ON DELETE CASCADE
)";

try {
    $connexion->exec($sql);
    echo "Table employe_documents created successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
