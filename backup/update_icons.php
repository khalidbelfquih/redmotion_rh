<?php
require_once 'config/connexion.php';

$updates = [
    'Retards' => 'bx bx-timer',
    'Finance & Paie' => 'bx bx-money',
    'Planning' => 'bx bx-calendar'
];

foreach ($updates as $label => $icon) {
    $sql = "UPDATE menu_items SET icon = ? WHERE label = ?";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([$icon, $label]);
    echo "Updated $label to $icon\n";
}
?>
