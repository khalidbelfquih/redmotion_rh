<?php
require_once 'model/function.php';

try {
    $sql = file_get_contents('create_menu_table.sql');
    $pdo = getPDO();
    $pdo->exec($sql);
    echo "Migration menu_items appliquée avec succès.";
} catch (PDOException $e) {
    echo "Erreur lors de la migration : " . $e->getMessage();
}
?>
