) {

    $sql = "UPDATE client SET nom=?, prenom=?, telephone=?, adresse=?, date_naissance=?, email=?, cin=?, permis_conduire=?, commentaires=? WHERE id=?";
    $req = $connexion->prepare($sql);

    $req->execute(array(
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $_POST['adresse'],
        $_POST['date_naissance'],
        $_POST['email'],
        $_POST['cin'],
        $_POST['permis_conduire'],
        $_POST['commentaires'],
        $_POST['id']
    ));

    if ($req->rowCount() != 0) {
        $_SESSION['message']['text'] = "Client modifié avec succès";
        $_SESSION['message']['type'] = "success";
    } else {
        $_SESSION['message']['text'] = "Aucune modification n'a été effectuée";
        $_SESSION['message']['type'] = "warning";
    }
} else {
    $_SESSION['message']['text'] = "Une information obligatoire est manquante";
    $_SESSION['message']['type'] = "danger";
}

header('Location: ../vue/client.php');