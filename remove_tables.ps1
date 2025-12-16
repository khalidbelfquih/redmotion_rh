$inputFile = "redmotion_rh.sql"
$outputFile = "redmotion_rh_clean.sql"

$tablesToRemove = @(
    "article",
    "categorie_article",
    "client",
    "commande",
    "commande_detail",
    "contact",
    "correction_optique",
    "details_optique",
    "details_prescription",
    "echeancier",
    "facture",
    "fournisseur",
    "garantie_ligne",
    "ligne_vente",
    "paiement",
    "vente",
    "vue_ventes_details",
    "vue_ventes_factures",
    "chauffeur",
    "depense",
    "location",
    "paiement_location",
    "voiture"
)

$reader = [System.IO.StreamReader]::new($inputFile)
$writer = [System.IO.StreamWriter]::new($outputFile)

$skip = $false
$currentTable = ""

while (($line = $reader.ReadLine()) -ne $null) {
    # Check for start of table structure
    if ($line -match '^CREATE TABLE `([^`]+)`') {
        $tableName = $matches[1]
        if ($tablesToRemove -contains $tableName) {
            $skip = $true
            $currentTable = $tableName
            Write-Host "Removing table structure: $tableName"
        } else {
            $skip = $false
        }
    }
    # Check for start of data dump
    elseif ($line -match '^INSERT INTO `([^`]+)`') {
        $tableName = $matches[1]
        if ($tablesToRemove -contains $tableName) {
            $skip = $true
            $currentTable = $tableName
            Write-Host "Removing data for: $tableName"
        } else {
            $skip = $false
        }
    }
    # Check for table comments
    elseif ($line -match '^-- Table structure for table `([^`]+)`') {
        $tableName = $matches[1]
        if ($tablesToRemove -contains $tableName) {
            $skip = $true
            $currentTable = $tableName
        } else {
            $skip = $false
        }
    }
    elseif ($line -match '^-- Dumping data for table `([^`]+)`') {
        $tableName = $matches[1]
        if ($tablesToRemove -contains $tableName) {
            $skip = $true
            $currentTable = $tableName
        } else {
            $skip = $false
        }
    }
    
    # Logic to stop skipping
    if ($skip) {
        # If we are skipping, we check if the line ends the statement.
        if ($line.Trim().EndsWith(";")) {
            $skip = $false
        }
    } else {
        $writer.WriteLine($line)
    }
}

$reader.Close()
$writer.Close()

Write-Host "Done. Cleaned file saved to $outputFile"
