<?php
// Créer le fichier vue/ajouter_versement.php
include 'entete.php';

// Vérifier si l'ID du paiement est fourni
if (empty($_GET['id'])) {
    $_SESSION['message']['text'] = "ID de paiement non spécifié";
    $_SESSION['message']['type'] = "danger";
    header("Location: paiements.php");
    exit();
}

$id_paiement = $_GET['id'];

// Récupérer les informations du paiement
$sql = "SELECT p.*, v.id as id_vente, v.date_vente, c.nom, c.prenom, a.nom_article 
        FROM paiement p
        JOIN vente v ON p.id_vente = v.id
        JOIN client c ON v.id_client = c.id
        JOIN article a ON v.id_article = a.id
        WHERE p.id = ?";
        
$req = $connexion->prepare($sql);
$req->execute([$id_paiement]);
$paiement = $req->fetch(PDO::FETCH_ASSOC);

if (!$paiement) {
    $_SESSION['message']['text'] = "Paiement introuvable";
    $_SESSION['message']['type'] = "danger";
    header("Location: paiements.php");
    exit();
}

// Si le paiement est déjà complet, rediriger
if ($paiement['statut'] === 'complet') {
    $_SESSION['message']['text'] = "Ce paiement est déjà complet";
    $_SESSION['message']['type'] = "info";
    header("Location: paiements.php");
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_versement'])) {
    try {
        require_once '../model/ajoutPaiement.php';
        
        $montant_paye_additionnel = floatval($_POST['montant_versement']);
        $reference_paiement = $_POST['reference_paiement'] ?? null;
        $mode_paiement = $_POST['mode_paiement'];
        
        // Mettre à jour le paiement
        updatePaiement($id_paiement, $montant_paye_additionnel, $reference_paiement, $mode_paiement);
        
        $_SESSION['message']['text'] = "Versement ajouté avec succès";
        $_SESSION['message']['type'] = "success";
        header("Location: paiements.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['message']['text'] = "Erreur: " . $e->getMessage();
        $_SESSION['message']['type'] = "danger";
    }
}
?>

<div class="home-content">
    <div class="overview-boxes">
        <div class="box" style="width: 100%; max-width: 800px; margin: 0 auto;">
            <h2 style="margin-bottom: 20px; color: #0a2558;">Ajouter un versement</h2>
            
            <div style="margin-bottom: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 5px;">
               <h3 style="color: #2a9d8f; margin-top: 0;">Informations sur le paiement</h3>
                
                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1; min-width: 200px;">
                        <p><strong>Client:</strong> <?= $paiement['nom'] . ' ' . $paiement['prenom'] ?></p>
                        <p><strong>Article:</strong> <?= $paiement['nom_article'] ?></p>
                        <p><strong>Date de vente:</strong> <?= date('d/m/Y', strtotime($paiement['date_vente'])) ?></p>
                    </div>
                    
                    <div style="flex: 1; min-width: 200px;">
                        <p><strong>Montant total:</strong> <?= number_format($paiement['montant_total'], 2, ',', ' ') ?> DH</p>
                        <p><strong>Déjà payé:</strong> <?= number_format($paiement['montant_paye'], 2, ',', ' ') ?> DH</p>
                        <p><strong>Reste à payer:</strong> 
                            <span style="font-weight: bold; color: #e76f51;">
                                <?= number_format($paiement['reste_a_payer'], 2, ',', ' ') ?> DH
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            
            <form action="" method="post">
                <div style="margin-bottom: 20px;">
                    <div style="margin-bottom: 15px;">
                        <label for="montant_versement">Montant du versement (DH)</label>
                        <input type="number" step="0.01" name="montant_versement" id="montant_versement" class="form-control" 
                            required max="<?= $paiement['reste_a_payer'] ?>" value="<?= $paiement['reste_a_payer'] ?>">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label for="mode_paiement">Mode de paiement</label>
                        <select name="mode_paiement" id="mode_paiement" class="form-control" required onchange="toggleReferenceField()">
                            <option value="especes">Espèces</option>
                            <option value="carte_bancaire">Carte bancaire</option>
                            <option value="cheque">Chèque</option>
                        </select>
                    </div>
                    
                    <div id="reference_container" style="margin-bottom: 15px; display: none;">
                        <label for="reference_paiement">Référence de paiement</label>
                        <input type="text" name="reference_paiement" id="reference_paiement" class="form-control" 
                            placeholder="N° transaction / N° chèque">
                    </div>
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" name="ajouter_versement" value="1" 
                        style="background-color: #2a9d8f; color: white; border: none; padding: 10px 20px; 
                        border-radius: 4px; cursor: pointer; margin-right: 10px;">
                        <i class='bx bx-check'></i> Enregistrer le versement
                    </button>
                    
                    <a href="paiements.php" 
                        style="background-color: #6c757d; color: white; text-decoration: none; 
                        padding: 10px 20px; border-radius: 4px; display: inline-block;">
                        <i class='bx bx-arrow-back'></i> Annuler
                    </a>
                </div>
            </form>
            
            <?php
            if (!empty($_SESSION['message']['text'])) {
            ?>
                <div class="alert <?= $_SESSION['message']['type'] ?>" style="margin-top: 20px;">
                    <?= $_SESSION['message']['text'] ?>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<script>
    function toggleReferenceField() {
        const modePaiement = document.getElementById('mode_paiement').value;
        const referenceContainer = document.getElementById('reference_container');
        
        if (modePaiement === 'carte_bancaire' || modePaiement === 'cheque') {
            referenceContainer.style.display = 'block';
            
            // Ajuster le placeholder en fonction du mode de paiement
            const placeholder = modePaiement === 'carte_bancaire' ? 'N° de transaction' : 'N° de chèque';
            document.getElementById('reference_paiement').placeholder = placeholder;
        } else {
            referenceContainer.style.display = 'none';
        }
    }
    
    // Initialiser l'affichage au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        toggleReferenceField();
    });
</script>

<style>
.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}
.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}
</style>

<?php
include 'pied.php';
?>