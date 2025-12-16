<?php
include 'entete.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

if (!empty($_GET['id'])) {
    $utilisateur = getUtilisateur($_GET['id']);
}
?>
<div class="home-content">
    <div class="overview-boxes">
        <div class="box">
            <form action=" <?= !empty($_GET['id']) ?  "../model/modifUtilisateur.php" : "../model/ajoutUtilisateur.php" ?>" method="post">
                <label for="nom">Nom</label>
                <input value="<?= !empty($_GET['id']) ?  $utilisateur['nom'] : "" ?>" type="text" name="nom" id="nom" placeholder="Veuillez saisir le nom" required>
                <input value="<?= !empty($_GET['id']) ?  $utilisateur['id'] : "" ?>" type="hidden" name="id" id="id">
                
                <label for="prenom">Prénom</label>
                <input value="<?= !empty($_GET['id']) ?  $utilisateur['prenom'] : "" ?>" type="text" name="prenom" id="prenom" placeholder="Veuillez saisir le prénom" required>

                <label for="email">Email</label>
                <input value="<?= !empty($_GET['id']) ?  $utilisateur['email'] : "" ?>" type="email" name="email" id="email" placeholder="Veuillez saisir l'email" required>
                
                <label for="password">Mot de passe <?= !empty($_GET['id']) ? "(laisser vide pour ne pas modifier)" : "" ?></label>
                <input type="password" name="password" id="password" placeholder="Veuillez saisir le mot de passe" <?= empty($_GET['id']) ? "required" : "" ?>>
                
                <label for="role">Rôle</label>
                <select name="role" id="role" required>
                    <option value="">--Choisir un rôle--</option>
                    <option value="admin" <?= !empty($_GET['id']) && $utilisateur['role'] == 'admin' ? "selected" : "" ?>>Admin</option>
                    <option value="utilisateur" <?= !empty($_GET['id']) && $utilisateur['role'] == 'utilisateur' ? "selected" : "" ?>>Utilisateur standard</option>
                </select>

                <button type="submit">Valider</button>

                <?php
                if (!empty($_SESSION['message']['text'])) {
                ?>
                    <div class="alert <?= $_SESSION['message']['type'] ?>">
                        <?= $_SESSION['message']['text'] ?>
                    </div>
                <?php
                }
                ?>
            </form>

        </div>
        <div class="box">
            <table class="mtable">
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Date de création</th>
                    <th>Action</th>
                </tr>
                <?php
                $utilisateurs = getUtilisateur();

                if (!empty($utilisateurs) && is_array($utilisateurs)) {
                    foreach ($utilisateurs as $key => $value) {
                ?>
                        <tr>
                            <td><?= $value['nom'] ?></td>
                            <td><?= $value['prenom'] ?></td>
                            <td><?= $value['email'] ?></td>
                            <td><?= $value['role'] == 'admin' ? 'Administrateur' : 'Utilisateur standard' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($value['date_creation'])) ?></td>
                            <td>
                                <a href="?id=<?= $value['id'] ?>"><i class='bx bx-edit-alt'></i></a>
                                <?php if ($value['id'] != $_SESSION['user']['id']): ?>
                                <a href="#" onclick="supprimerUtilisateur(<?= $value['id'] ?>)"><i class='bx bx-trash' style="color: red;"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
            </table>
        </div>
    </div>
</div>
</section>

<?php
include 'pied.php';
?>

<script>
function supprimerUtilisateur(id) {
    if(confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")) {
        window.location.href = "../model/supprimerUtilisateur.php?id=" + id;
    }
}
</script>