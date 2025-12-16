<?php
include 'config/connexion.php';

try {
    // Check if column file_data exists
    $check = $connexion->query("SHOW COLUMNS FROM employe_documents LIKE 'file_data'");
    
    if ($check->rowCount() == 0) {
        // Add new columns for BLOB storage
        $connexion->exec("ALTER TABLE employe_documents ADD COLUMN file_data LONGBLOB AFTER type");
        $connexion->exec("ALTER TABLE employe_documents ADD COLUMN mime_type VARCHAR(100) AFTER file_data");
        $connexion->exec("ALTER TABLE employe_documents ADD COLUMN file_name VARCHAR(255) AFTER mime_type");
        
        // Make 'fichier' nullable as we might stop using it, or drop it later
        // We will keep it for now to avoid breaking existing selects immediately, but we won't use it for new uploads
        $connexion->exec("ALTER TABLE employe_documents MODIFY COLUMN fichier VARCHAR(255) NULL");
        
        echo "Database schema updated successfully for BLOB storage.";
    } else {
        echo "Database schema already updated.";
    }
    
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>
