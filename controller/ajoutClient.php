
    $sql = "INSERT INTO client(nom, prenom, telephone, adresse, date_naissance, email, cin, permis_conduire, commentaires)
            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
        $_POST['commentaires']
    ));

    if ($req->rowCount() != 0) {
        $_SESSION['message']['text'] = "Client ajouté avec succès";
        $_SESSION['message']['type'] = "success";
    } else {
        $_SESSION['message']['text'] = "Une erreur s'est produite lors de l'ajout du client";
        $_SESSION['message']['type'] = "danger";
    }
} else {
    $_SESSION['message']['text'] = "Une information obligatoire est manquante";
    $_SESSION['message']['type'] = "danger";
}

header('Location: ../vue/client.php');