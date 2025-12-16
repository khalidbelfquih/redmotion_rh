<?php
include_once 'config/connexion.php';

$sql = "INSERT INTO menu_items (label, link, icon, position, role_required) 
        SELECT * FROM (SELECT 'Congés', 'conges.php', 'bx bx-calendar-event', 10, NULL) AS tmp
        WHERE NOT EXISTS (SELECT * FROM menu_items WHERE link = 'conges.php') LIMIT 1";

try {
    $connexion->exec($sql);
    echo "Menu item 'Congés' added successfully.";
} catch (PDOException $e) {
    echo "Error adding menu item: " . $e->getMessage();
}
?>
