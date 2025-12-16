<?php
// Configuration de la gestion des erreurs

// Désactiver l'affichage des erreurs à l'écran
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Activer le journalisation des erreurs
ini_set('log_errors', 1);

// Définir le chemin du fichier de log
// Utilisation de __DIR__ pour un chemin absolu fiable
$log_file = __DIR__ . '/../logs/app_error.log';
ini_set('error_log', $log_file);

// Rapporter toutes les erreurs
error_reporting(E_ALL);
