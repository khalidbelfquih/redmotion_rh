<?php
// Créer le fichier vue/payer_echeance.php
include 'entete.php';

// Vérifier si l'ID de l'échéance est fourni
if (empty($_GET['id'])) {
    $_SESSION['message']['text'] = "ID d'échéance non spécifié";
    $_SESSION['message']['type'] = "danger";
    header("Location: paiements.php");
    exit();
}

$id_echeance = $_GET['id'];

// Récupérer les informations de l'échéance
$sql = "SELECT e.*, p.id_vente, p.montant_total, p.montant_paye, p.reste_a_payer,
               v.date_vente, c.nom, c.prenom, a.nom_article 
        FROM echeancier e
        JOIN paiement p ON e.id_paiement = p.id
        JOIN vente v ON p.id_vente = v.id
        JOIN client c ON v.id_client = c.id
        JOIN article a ON v.id_article = a.id
        WHERE e.id = ?";
        
$req = $connexion->prepare($sql);
$req->execute([$id_echeance]);
$echeance = $req->fetch(PDO::FETCH_ASSOC);

if (!$echeance) {
    $_SESSION['message']['text'] = "Échéance introuvable";
    $_SESSION['message']['type'] = "danger";
    header("Location: paiements.php");
    exit();
}

// Si l'échéance est déjà payée, rediriger
if ($echeance['statut'] === 'paye') {
    $_SESSION['message']['text'] = "Cette échéance est déjà marquée comme payée";
    $_SESSION['message']['type'] = "info";
    header("Location: detail_paiement.php?id=" . $echeance['id_paiement']);
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payer_echeance'])) {
    try {
        // Commencer une transaction
        $connexion->beginTransaction();
        
        // Mettre à jour l'échéance
        $sql = "UPDATE echeancier SET 
                statut = 'paye', 
                date_paiement_effectif = ?, 
                mode_paiement = ?, 
                reference = ? 
                WHERE id = ?";
                
        $req = $connexion->prepare($sql);
        $req->execute([
            $_POST['date_paiement'],
            $_POST['mode_paiement'],
            $_POST['reference_paiement'] ?? null,
            $id_echeance
        ]);
        
        // Mettre à jour le paiement principal
        $nouveau_montant_paye = $echeance['montant_paye'] + $echeance['montant'];
        $nouveau_reste_a_payer = $echeance['montant_total'] - $nouveau_montant_paye;
        
        // Déterminer le nouveau statut
        $statut = 'en_attente';
        if ($nouveau_reste_a_payer <= 0) {
            $statut = 'complet';
        } else if ($nouveau_montant_paye > 0) {
            $statut = 'partiel';
        }
        
        $sql = "UPDATE paiement SET 
                montant_paye = ?, 
                reste_a_payer = ?, 
                statut = ? 
                WHERE id = ?";
                
        $req = $connexion->prepare($sql);
        $req->execute([
            $nouveau_montant_paye,
            $nouveau_reste_a_payer,
            $statut,
            $echeance['id_paiement']
        ]);
        
        // Valider la transaction
        $connexion->commit();
        
        $_SESSION['message']['text'] = "Échéance marquée comme payée avec succès";
        $_SESSION['message']['type'] = "success";
        header("Location: detail_paiement.php?id=" . $echeance['id_paiement']);
        exit();
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $connexion->rollBack();
        
        $_SESSION['message']['text'] = "Erreur: " . $e->getMessage();
        $_SESSION['message']['type'] = "danger";
    }
}
?>

<div class="home-content">
    <div class="overview-boxes">
        <div class="box" style="width: 100%; max-width: 600px; margin: 0 auto;">
            <h2 style="margin-bottom: 20px; color: #0a2558;">Paiement d'échéance</h2>
            
            <div style="margin-bottom: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 5px;">
                <h3 style="color: #2a9d8f; margin-top: 0;">Informations sur l'échéance</h3>
                
                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1; min-width: 200px;">
                        <p><strong>Client:</strong> <?= $echeance['nom'] . ' ' . $echeance['prenom'] ?></p>
                        <p><strong>Article:</strong> <?= $echeance['nom_article'] ?></p>
                        <p><strong>Montant de l'échéance:</strong> 
                            <span style="font-weight: bold; color: #e76f51;">
                                <?= number_format($echeance['montant'], 2, ',', ' ') ?> DH
                            </span>
                        </p>
                    </div>
                    
                    <div style="flex: 1; min-width: 200px;">
                        <p><strong>Date d'échéance:</strong> <?= date('d/m/Y', strtotime($echeance['date_echeance'])) ?></p>
                        <p><strong>Statut actuel:</strong> 
                            <span class="badge <?= ($echeance['statut'] == 'en_retard') ? 'bg-danger' : 'bg-info' ?>">
                                <span class="badge <?= ($echeance['statut'] == 'en_retard') ? 'bg-danger' : 'bg-info' ?>">
                                <?= ($echeance['statut'] == 'en_retard') ? 'En retard' : 'À venir' ?>
                            </span>
                        </p>
                        <?php if ($echeance['statut'] == 'en_retard'): ?>
                        <p><strong>Jours de retard:</strong> 
                            <?= floor((time() - strtotime($echeance['date_echeance'])) / (60 * 60 * 24)) ?> jours
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <form action="" method="post">
                <div style="margin-bottom: 20px;">
                    <div style="margin-bottom: 15px;">
                        <label for="date_paiement">Date de paiement</label>
                        <input type="date" name="date_paiement" id="date_paiement" class="form-control" 
                            required value="<?= date('Y-m-d') ?>">
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
                    <button type="submit" name="payer_echeance" value="1" 
                        style="background-color: #2a9d8f; color: white; border: none; padding: 10px 20px; 
                        border-radius: 4px; cursor: pointer; margin-right: 10px;">
                        <i class='bx bx-check'></i> Marquer comme payé
                    </button>
                    
                    <a href="detail_paiement.php?id=<?= $echeance['id_paiement'] ?>" 
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
.badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    color: white;
}
.bg-danger {
    background-color: #e76f51;
}
.bg-info {
    background-color: #4d908e;
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