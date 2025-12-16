<?php
session_start();
include __DIR__ . '/../model/hr_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('HTTP/1.0 403 Forbidden');
    echo "Access denied.";
    exit();
}

if (!isset($_GET['id'])) {
    header('HTTP/1.0 400 Bad Request');
    echo "ID not provided.";
    exit();
}

$id = $_GET['id'];
$doc = getDocumentContent($id);

if ($doc && !empty($doc['file_data'])) {
    // If we have access control, check $doc ownership or permissions here
    // e.g., if ($doc['id_employe'] != $_SESSION['user']['id'] && !isAdmin()) { ... }
    
    // Set headers
    $size = strlen($doc['file_data']);
    $filename = !empty($doc['file_name']) ? $doc['file_name'] : 'document_' . $id;
    
    // Clean filename
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
    
    if (!empty($doc['mime_type'])) {
        header("Content-Type: " . $doc['mime_type']);
    } else {
        header("Content-Type: application/octet-stream");
    }
    
    header("Content-Length: " . $size);
    header("Content-Transfer-Encoding: binary");
    header("Cache-Control: private, max-age=0, must-revalidate");
    header("Pragma: public");
    header("Content-Disposition: inline; filename=\"" . $filename . "\"");
    
    // Output data
    echo $doc['file_data'];
} else {
    // Fallback if file data is missing but path exists (legacy support)
    // We would need to fetch 'fichier' column by updating getDocumentContent query
    // But for this request, assuming we only care about new BLOBs
    header('HTTP/1.0 404 Not Found');
    echo "File content not found.";
}
?>
