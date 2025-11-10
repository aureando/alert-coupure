<?php
/**
 * Autoloader PSR-4
 * Charge automatiquement les classes PHP sans avoir à faire des require/include partout
 */

// Enregistrer la fonction d'autoload
spl_autoload_register(function ($class) {
    
    // Namespace de base du projet
    $prefix = 'App\\';
    
    // Dossier de base
    $baseDir = __DIR__ . '/app/';
    
    // Vérifier si la classe utilise notre namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Cette classe n'utilise pas notre namespace, on la laisse
        return;
    }
    
    // Obtenir le nom relatif de la classe
    $relativeClass = substr($class, $len);
    
    // Remplacer les backslash par des slashes et ajouter .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    // Si le fichier existe, on l'inclut
    if (file_exists($file)) {
        require $file;
    }
});
