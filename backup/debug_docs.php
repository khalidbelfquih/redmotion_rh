<?php
include 'config/connexion.php';
$stmt = $connexion->query("SELECT * FROM employe_documents");
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($docs);
echo "</pre>";

// Also check file system
echo "<h2>File System Check</h2>";
foreach($docs as $doc) {
    $path = $doc['fichier'];
    $fullPath = __DIR__ . '/' . $path; // assuming path is relative to root like 'public/...'
    echo "Checking: " . $path . "<br>";
    echo "Full Path: " . $fullPath . "<br>";
    if(file_exists($fullPath)) {
        echo "<span style='color:green'>FOUND</span><br>";
    } else {
        echo "<span style='color:red'>NOT FOUND</span><br>";
    }
    echo "<hr>";
}
?>
