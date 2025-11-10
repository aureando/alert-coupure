<?php

namespace App\Controllers;

/**
 * Classe BaseController
 * Tous les contrôleurs héritent de cette classe
 */
class BaseController
{
    /**
     * Charger une vue
     * 
     * @param string $view Nom de la vue (ex: 'auth/login')
     * @param array $data Données à passer à la vue
     * @param string $layout Layout à utiliser ('main' ou 'admin')
     * @return void
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Démarrer la capture de sortie
        ob_start();
        
        // Inclure la vue
        $viewPath = APP_PATH . "/Views/{$view}.php";
        
        if (!file_exists($viewPath)) {
            throw new \Exception("Vue introuvable : {$view}");
        }
        
        require $viewPath;
        
        // Récupérer le contenu de la vue
        $content = ob_get_clean();
        
        // Inclure le layout avec le contenu
        $layoutPath = APP_PATH . "/Views/layouts/{$layout}.php";
        
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout introuvable : {$layout}");
        }
        
        require $layoutPath;
    }
    
    /**
     * Retourner une réponse JSON
     * 
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Rediriger avec message flash
     * 
     * @param string $path
     * @param string $type
     * @param string $message
     * @return void
     */
    protected function redirectWith(string $path, string $type, string $message): void
    {
        redirect_with($path, $type, $message);
    }
    
    /**
     * Vérifier l'authentification
     * 
     * @return void
     */
    protected function requireAuth(): void
    {
        require_auth();
    }
    
    /**
     * Vérifier le rôle admin
     * 
     * @return void
     */
    protected function requireAdmin(): void
    {
        require_admin();
    }
}