<?php
// traiter_excel_articles_final.php
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Headers JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Fonction de log
function logDebug($message, $data = null) {
    $logMessage = date('[Y-m-d H:i:s] ') . $message;
    if ($data !== null) {
        $logMessage .= ': ' . json_encode($data);
    }
    error_log($logMessage);
}

// Connexion à la base
$connexion = null;
try {
    if (file_exists('model/connexion.php')) {
        require_once 'model/connexion.php';
        logDebug("Connexion incluse depuis model/connexion.php");
    } else {
        throw new Exception('Fichier model/connexion.php non trouvé');
    }
    
    // Vérifier les variables de connexion
    if (isset($connexion)) {
        $db = $connexion;
    } elseif (isset($pdo)) {
        $db = $pdo;
    } elseif (isset($conn)) {
        $db = $conn;
    } else {
        throw new Exception('Variable de connexion non trouvée');
    }
    
    // Test de la connexion
    $db->query('SELECT 1');
    logDebug("Connexion OK");
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur de connexion: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Vérifier le fichier uploadé
if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== 0) {
    echo json_encode(['error' => 'Aucun fichier reçu ou erreur d\'upload']);
    exit;
}

$file = $_FILES['excel_file'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];

logDebug("Fichier reçu", ['name' => $fileName, 'size' => $fileSize]);

// Vérifier la taille
if ($fileSize > 10 * 1024 * 1024) {
    echo json_encode(['error' => 'Fichier trop volumineux (max 10MB)']);
    exit;
}

// Vérifier l'extension
$allowedExtensions = ['xlsx', 'csv'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($fileExtension, $allowedExtensions)) {
    echo json_encode(['error' => 'Format non supporté. Utilisez .xlsx ou .csv']);
    exit;
}

// Fonction de validation
function validateArticleData($row) {
    $errors = [];
    
    if (empty(trim($row['nom_article'] ?? ''))) {
        $errors[] = 'Nom article requis';
    }
    
    if (empty(trim($row['reference'] ?? ''))) {
        $errors[] = 'Référence requise';
    }
    
    if (empty(trim($row['marque'] ?? ''))) {
        $errors[] = 'Marque requise';
    }
    
    if (empty(trim($row['modele'] ?? ''))) {
        $errors[] = 'Modèle requis';
    }
    
    return $errors;
}

// Fonction de traitement des données
function processArticleData($articleData) {
    // Valeurs par défaut
    $articleData['quantite'] = isset($articleData['quantite']) && $articleData['quantite'] !== '' ? (int)$articleData['quantite'] : 0;
    $articleData['couleur'] = $articleData['couleur'] ?? '';
    $articleData['matiere'] = $articleData['matiere'] ?? '';
    $articleData['forme'] = $articleData['forme'] ?? '';
    $articleData['diametre'] = $articleData['diametre'] ?? '';
    $articleData['emplacement'] = $articleData['emplacement'] ?? '';
    $articleData['societe'] = $articleData['societe'] ?? '';
    
    // Prix et catégorie
    $articleData['prix_unitaire'] = isset($articleData['prix_unitaire']) && $articleData['prix_unitaire'] !== '' ? (float)$articleData['prix_unitaire'] : 0.0;
    $articleData['id_categorie'] = isset($articleData['id_categorie']) && $articleData['id_categorie'] !== '' ? (int)$articleData['id_categorie'] : 1;
    
    // Dates
    if (empty($articleData['date_fabrication'])) {
        $articleData['date_fabrication'] = date('Y-m-d H:i:s');
    } else {
        $dateStr = $articleData['date_fabrication'];
        if (strpos($dateStr, 'T') !== false) {
            $dateStr = str_replace('T', ' ', $dateStr);
            $dateStr = substr($dateStr, 0, 19);
        }
        if (strtotime($dateStr)) {
            $articleData['date_fabrication'] = date('Y-m-d H:i:s', strtotime($dateStr));
        } else {
            $articleData['date_fabrication'] = date('Y-m-d H:i:s');
        }
    }
    
    if (empty($articleData['date_expiration'])) {
        $articleData['date_expiration'] = date('Y-m-d H:i:s', strtotime('+1 year'));
    } else {
        $dateStr = $articleData['date_expiration'];
        if (strpos($dateStr, 'T') !== false) {
            $dateStr = str_replace('T', ' ', $dateStr);
            $dateStr = substr($dateStr, 0, 19);
        }
        if (strtotime($dateStr)) {
            $articleData['date_expiration'] = date('Y-m-d H:i:s', strtotime($dateStr));
        } else {
            $articleData['date_expiration'] = date('Y-m-d H:i:s', strtotime('+1 year'));
        }
    }
    
    // Validation
    $errors = validateArticleData($articleData);
    $articleData['valid'] = empty($errors);
    $articleData['errors'] = $errors;
    
    return $articleData;
}

// Fonction pour traiter CSV
function processCSV($filePath) {
    $data = [];
    
    $content = file_get_contents($filePath);
    $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252']);
    
    if ($encoding !== 'UTF-8') {
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        file_put_contents($filePath, $content);
    }
    
    // Détecter le séparateur
    $separators = [';', ',', "\t"];
    $separator = ',';
    $maxCount = 0;
    
    foreach ($separators as $sep) {
        $count = substr_count($content, $sep);
        if ($count > $maxCount) {
            $maxCount = $count;
            $separator = $sep;
        }
    }
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        $headers = fgetcsv($handle, 1000, $separator);
        
        if (!$headers) {
            fclose($handle);
            throw new Exception('Impossible de lire les en-têtes');
        }
        
        $headers = array_map('trim', $headers);
        
        while (($row = fgetcsv($handle, 1000, $separator)) !== FALSE) {
            if (count($row) === count($headers)) {
                $articleData = array_combine($headers, $row);
                $articleData = array_map('trim', $articleData);
                
                // Ignorer les lignes vides
                $hasData = false;
                foreach ($articleData as $value) {
                    if (!empty($value)) {
                        $hasData = true;
                        break;
                    }
                }
                
                if ($hasData) {
                    $data[] = processArticleData($articleData);
                }
            }
        }
        fclose($handle);
    }
    
    return $data;
}

// Fonction pour traiter XLSX (simple)
function processExcelXLSX($filePath) {
    if (!extension_loaded('zip')) {
        throw new Exception('Extension ZIP requise');
    }
    
    $tempDir = sys_get_temp_dir() . '/' . uniqid('excel_');
    mkdir($tempDir);
    
    $zip = new ZipArchive;
    if ($zip->open($filePath) !== TRUE) {
        rmdir($tempDir);
        throw new Exception('Impossible d\'ouvrir le fichier Excel');
    }
    
    $zip->extractTo($tempDir);
    $zip->close();
    
    $data = [];
    
    try {
        $sheetFile = $tempDir . '/xl/worksheets/sheet1.xml';
        $sharedStringsFile = $tempDir . '/xl/sharedStrings.xml';
        
        if (!file_exists($sheetFile)) {
            throw new Exception('Feuille non trouvée');
        }
        
        // Lire les chaînes partagées
        $sharedStrings = [];
        if (file_exists($sharedStringsFile)) {
            $sharedStringsXML = simplexml_load_file($sharedStringsFile);
            if ($sharedStringsXML) {
                foreach ($sharedStringsXML->si as $si) {
                    $sharedStrings[] = (string)$si->t;
                }
            }
        }
        
        // Lire les données
        $sheetXML = simplexml_load_file($sheetFile);
        if (!$sheetXML) {
            throw new Exception('Impossible de lire les données');
        }
        
        $rows = [];
        
        if (isset($sheetXML->sheetData->row)) {
            foreach ($sheetXML->sheetData->row as $row) {
                $rowData = [];
                
                if (isset($row->c)) {
                    foreach ($row->c as $cell) {
                        $value = '';
                        if (isset($cell->v)) {
                            if (isset($cell['t']) && $cell['t'] == 's') {
                                $stringIndex = (int)$cell->v;
                                $value = isset($sharedStrings[$stringIndex]) ? $sharedStrings[$stringIndex] : '';
                            } else {
                                $value = (string)$cell->v;
                            }
                        }
                        $rowData[] = $value;
                    }
                }
                
                $rows[] = $rowData;
            }
        }
        
        // Convertir avec en-têtes
        if (count($rows) > 0) {
            $headers = $rows[0];
            
            for ($i = 1; $i < count($rows); $i++) {
                $articleData = [];
                for ($j = 0; $j < count($headers); $j++) {
                    $header = trim($headers[$j]);
                    $value = isset($rows[$i][$j]) ? $rows[$i][$j] : '';
                    $articleData[$header] = $value;
                }
                
                // Ignorer les lignes vides
                $hasData = false;
                foreach ($articleData as $value) {
                    if (!empty(trim($value))) {
                        $hasData = true;
                        break;
                    }
                }
                
                if ($hasData) {
                    $data[] = processArticleData($articleData);
                }
            }
        }
        
    } finally {
        // Nettoyer
        if (is_dir($tempDir)) {
            $files = array_diff(scandir($tempDir), array('.', '..'));
            foreach ($files as $file) {
                $filePath = $tempDir . '/' . $file;
                if (is_dir($filePath)) {
                    $subfiles = array_diff(scandir($filePath), array('.', '..'));
                    foreach ($subfiles as $subfile) {
                        unlink($filePath . '/' . $subfile);
                    }
                    rmdir($filePath);
                } else {
                    unlink($filePath);
                }
            }
            rmdir($tempDir);
        }
    }
    
    return $data;
}

// Traitement principal
try {
    $processedData = [];
    
    if ($fileExtension === 'csv') {
        $processedData = processCSV($fileTmpName);
    } elseif ($fileExtension === 'xlsx') {
        $processedData = processExcelXLSX($fileTmpName);
    }
    
    // Statistiques
    $totalRows = count($processedData);
    $validRows = count(array_filter($processedData, function($row) { return $row['valid']; }));
    $errorRows = $totalRows - $validRows;
    
    // Réponse
    $response = [
        'success' => true,
        'data' => $processedData,
        'stats' => [
            'total' => $totalRows,
            'valid' => $validRows,
            'errors' => $errorRows
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    logDebug("Erreur", $e->getMessage());
    echo json_encode(['error' => 'Erreur: ' . $e->getMessage()]);
}
?>