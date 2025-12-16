<?php
include 'config/connexion.php';
$sql = file_get_contents('fix_menu_link.sql');
try {
    $connexion->exec($sql);
    echo "Lien du menu corrigé avec succès.";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
