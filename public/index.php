<?php

/**
 * Point d'entrée de l'application Alert Coupure
 * Toutes les requêtes passent par ce fichier
 */

// Démarrer la session
session_start();

// // DEBUG - À RETIRER APRÈS
// session_destroy();
// session_start();
// // FIN DEBUG

// Charger la configuration
require_once __DIR__ . '/../app/Config/config.php';

// Charger l'autoloader
require_once __DIR__ . '/../autoload.php';

// Charger les helpers
require_once APP_PATH . '/Helpers/functions.php';
require_once APP_PATH . '/Helpers/auth.php';

// Importer les classes nécessaires
use App\Core\Router;
use App\Core\Request;
use App\Core\Session;

// Démarrer la session
Session::start();

// Créer le router et la requête
$router = new Router();
$request = new Request();

// DEBUG - Afficher l'URI détectée (RETIRE ÇA APRÈS)
if (ENVIRONMENT === 'development') {
    // Décommenter pour voir l'URI
    // echo "URI détectée: [" . $request->getUriPath() . "]<br>";
    // echo "Méthode: " . $request->getMethod() . "<br>";
}

// ============================================
// DÉFINITION DES ROUTES
// ============================================

// --- ROUTES PUBLIQUES ---
$router->get('/', 'HomeController@index');

// --- AUTHENTIFICATION ---
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// --- ROUTES UTILISATEUR (nécessitent authentification) ---
$router->get('/dashboard', 'DashboardController@index');

// Signalements
$router->get('/signalements', 'SignalementController@index');
$router->get('/signalements/create', 'SignalementController@create');
$router->post('/signalements', 'SignalementController@store');
$router->get('/signalements/{id}', 'SignalementController@show');

// --- ROUTES ADMIN (nécessitent rôle admin) ---
$router->group('/admin', function($router) {
    
    // Dashboard admin
    $router->get('', 'Admin\AdminDashboardController@index');
    $router->get('/', 'Admin\AdminDashboardController@index');
    
    // Gestion des villes
    $router->get('/villes', 'Admin\VilleController@index');
    $router->get('/villes/create', 'Admin\VilleController@create');
    $router->post('/villes', 'Admin\VilleController@store');
    $router->get('/villes/{id}', 'Admin\VilleController@show');
    $router->get('/villes/{id}/edit', 'Admin\VilleController@edit');
    $router->post('/villes/{id}/update', 'Admin\VilleController@update');
    $router->post('/villes/{id}/delete', 'Admin\VilleController@delete');
    
    // Gestion des quartiers
    $router->get('/quartiers', 'Admin\QuartierController@index');
    $router->get('/quartiers/create', 'Admin\QuartierController@create');
    $router->post('/quartiers', 'Admin\QuartierController@store');
    $router->get('/quartiers/{id}/edit', 'Admin\QuartierController@edit');
    $router->post('/quartiers/{id}/update', 'Admin\QuartierController@update');
    $router->post('/quartiers/{id}/delete', 'Admin\QuartierController@delete');
    
    // Gestion des coupures
    $router->get('/coupures', 'Admin\CoupureController@index');
    $router->get('/coupures/create', 'Admin\CoupureController@create');
    $router->post('/coupures', 'Admin\CoupureController@store');
    $router->get('/coupures/{id}', 'Admin\CoupureController@show');
    $router->get('/coupures/{id}/edit', 'Admin\CoupureController@edit');
    $router->post('/coupures/{id}/update', 'Admin\CoupureController@update');
    $router->post('/coupures/{id}/delete', 'Admin\CoupureController@delete');
    
    // Gestion des signalements
    $router->get('/signalements', 'Admin\SignalementAdminController@index');
    $router->get('/signalements/{id}', 'Admin\SignalementAdminController@show');
    $router->post('/signalements/{id}/update-status', 'Admin\SignalementAdminController@updateStatus');
    
    // Gestion des utilisateurs
    $router->get('/users', 'Admin\UserController@index');
    $router->get('/users/{id}', 'Admin\UserController@show');
    $router->post('/users/{id}/toggle-status', 'Admin\UserController@toggleStatus');
    
});

// --- AJAX (pour récupérer quartiers par ville) ---
$router->get('/api/quartiers/{ville_id}', 'ApiController@getQuartiersByVille');

// --- 404 ---
$router->setNotFoundHandler(function() {
    http_response_code(404);
    echo "404 - Page non trouvée";
});

// ============================================
// DISPATCHER LA REQUÊTE
// ============================================

try {
    $router->dispatch($request);
} catch (Exception $e) {
    // En développement : afficher l'erreur
    if (ENVIRONMENT === 'development') {
        echo "<h1>Erreur</h1>";
        echo "<p><strong>Message :</strong> " . $e->getMessage() . "</p>";
        echo "<p><strong>Fichier :</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        // En production : page d'erreur générique
        http_response_code(500);
        echo "Une erreur est survenue. Veuillez réessayer plus tard.";
    }
}