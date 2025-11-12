<?php

namespace App\Core;

/**
 * Classe Router
 * Gestion du routage de l'application (URLs → Controllers)
 */
class Router
{
    /**
     * Routes enregistrées (GET)
     * @var array
     */
    private array $getRoutes = [];

    /**
     * Routes enregistrées (POST)
     * @var array
     */
    private array $postRoutes = [];

    /**
     * Route par défaut (404)
     * @var callable|null
     */
    private $notFoundHandler = null;

    /**
     * Préfixe de groupe (pour routes admin par exemple)
     * @var string
     */
    private string $groupPrefix = '';

    /**
     * Middleware de groupe
     * @var array
     */
    private array $groupMiddleware = [];

    /**
     * Enregistrer une route GET
     * 
     * @param string $path Chemin de la route (ex: /login)
     * @param string|callable $handler Controller@method ou fonction
     * @param array $middleware Middlewares à appliquer
     * @return self
     */
    public function get(string $path, $handler, array $middleware = []): self
    {
        $path = $this->groupPrefix . $path;
        $middleware = array_merge($this->groupMiddleware, $middleware);
        
        $this->getRoutes[$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
        
        return $this;
    }

    /**
     * Enregistrer une route POST
     * 
     * @param string $path
     * @param string|callable $handler
     * @param array $middleware
     * @return self
     */
    public function post(string $path, $handler, array $middleware = []): self
    {
        $path = $this->groupPrefix . $path;
        $middleware = array_merge($this->groupMiddleware, $middleware);
        
        $this->postRoutes[$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
        
        return $this;
    }

    /**
     * Définir un groupe de routes avec préfixe et middleware
     * 
     * @param string $prefix Préfixe (ex: /admin)
     * @param callable $callback Fonction contenant les routes
     * @param array $middleware Middlewares à appliquer au groupe
     * @return void
     */
    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;
        
        $this->groupPrefix = $previousPrefix . $prefix;
        $this->groupMiddleware = array_merge($previousMiddleware, $middleware);
        
        // Exécuter le callback avec les routes
        $callback($this);
        
        // Restaurer les valeurs précédentes
        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    /**
     * Définir le handler pour les 404
     * 
     * @param callable $handler
     * @return void
     */
    public function setNotFoundHandler(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    /**
     * Dispatcher la requête vers le bon contrôleur
     * 
     * @param Request $request
     * @return mixed
     */
    public function dispatch(Request $request)
    {
        $method = $request->getMethod();
        $uri = $request->getUriPath();
        
        // Normaliser l'URI
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        // Choisir les routes selon la méthode
        $routes = ($method === 'GET') ? $this->getRoutes : $this->postRoutes;

        // Chercher une correspondance exacte
        foreach ($routes as $pattern => $route) {
            $normalizedPattern = rtrim($pattern, '/');
            if ($normalizedPattern === '') {
                $normalizedPattern = '/';
            }
            
            if ($normalizedPattern === $uri) {
                return $this->executeRoute($route, $request);
            }
        }

        // Chercher une correspondance avec paramètres (ex: /user/{id})
        foreach ($routes as $pattern => $route) {
            $params = $this->matchRoute($pattern, $uri);
            
            if ($params !== false) {
                return $this->executeRoute($route, $request, $params);
            }
        }

        // Aucune route trouvée - 404
        return $this->handleNotFound();
    }

    /**
     * Vérifier si une route correspond avec paramètres
     * 
     * @param string $pattern Pattern de la route (ex: /user/{id})
     * @param string $uri URI actuelle
     * @return array|false Paramètres extraits ou false
     */
    private function matchRoute(string $pattern, string $uri)
    {
        // Convertir {param} en regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            // Extraire seulement les paramètres nommés
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return $params;
        }

        return false;
    }

    /**
     * Exécuter une route
     * 
     * @param array $route
     * @param Request $request
     * @param array $params Paramètres de l'URL
     * @return mixed
     */
    private function executeRoute(array $route, Request $request, array $params = [])
    {
        // Exécuter les middlewares
        foreach ($route['middleware'] as $middleware) {
            $result = $this->executeMiddleware($middleware, $request);
            
            // Si le middleware retourne quelque chose, on arrête
            if ($result !== null) {
                return $result;
            }
        }

        // Exécuter le handler
        $handler = $route['handler'];

        // Si c'est une string "Controller@method"
        if (is_string($handler) && strpos($handler, '@') !== false) {
            return $this->callControllerMethod($handler, $request, $params);
        }

        // Si c'est un callable (fonction anonyme)
        if (is_callable($handler)) {
            return call_user_func_array($handler, [$request, $params]);
        }

        throw new \Exception("Handler invalide pour la route");
    }

    /**
     * Appeler une méthode de contrôleur
     * 
     * @param string $handler Format: "ControllerName@methodName"
     * @param Request $request
     * @param array $params
     * @return mixed
     */
    private function callControllerMethod(string $handler, Request $request, array $params = [])
    {
        [$controllerName, $method] = explode('@', $handler);

        // Construire le namespace complet
        // Si le contrôleur contient déjà Admin\ ou autre namespace, on le garde
        if (strpos($controllerName, '\\') !== false) {
            $controllerClass = "App\\Controllers\\{$controllerName}";
        } else {
            $controllerClass = "App\\Controllers\\{$controllerName}";
        }

        // Vérifier que la classe existe
        if (!class_exists($controllerClass)) {
            throw new \Exception("Contrôleur introuvable : {$controllerClass}");
        }

        // Instancier le contrôleur
        $controller = new $controllerClass();

        // Vérifier que la méthode existe
        if (!method_exists($controller, $method)) {
            throw new \Exception("Méthode introuvable : {$method} dans {$controllerClass}");
        }

        // Appeler la méthode avec les paramètres
        return call_user_func_array([$controller, $method], [$request, $params]);
    }

    /**
     * Exécuter un middleware
     * 
     * @param string $middleware Nom de la classe middleware
     * @param Request $request
     * @return mixed|null
     */
    private function executeMiddleware(string $middleware, Request $request)
    {
        // Si c'est un nom court (ex: 'auth'), on le résout
        $middlewareClass = $this->resolveMiddleware($middleware);

        if (!class_exists($middlewareClass)) {
            throw new \Exception("Middleware introuvable : {$middlewareClass}");
        }

        $middlewareInstance = new $middlewareClass();

        if (!method_exists($middlewareInstance, 'handle')) {
            throw new \Exception("Le middleware doit avoir une méthode handle()");
        }

        return $middlewareInstance->handle($request);
    }

    /**
     * Résoudre le nom d'un middleware
     * 
     * @param string $name
     * @return string
     */
    private function resolveMiddleware(string $name): string
    {
        $middlewareMap = [
            'auth' => 'App\\Middleware\\AuthMiddleware',
            'admin' => 'App\\Middleware\\AdminMiddleware',
            'guest' => 'App\\Middleware\\GuestMiddleware',
        ];

        return $middlewareMap[$name] ?? $name;
    }

    /**
     * Gérer les erreurs 404
     * 
     * @return void
     */
    private function handleNotFound()
    {
        // Si un handler 404 est défini
        if ($this->notFoundHandler !== null) {
            return call_user_func($this->notFoundHandler);
        }

        // Handler par défaut
        http_response_code(404);
        echo "404 - Page non trouvée";
        exit;
    }

    /**
     * Générer une URL vers une route
     * 
     * @param string $path
     * @param array $params Paramètres à remplacer dans l'URL
     * @return string
     */
    public static function url(string $path, array $params = []): string
    {
        // Remplacer les paramètres dans l'URL
        foreach ($params as $key => $value) {
            $path = str_replace("{{$key}}", $value, $path);
        }

        // Ajouter le BASE_URL
        return BASE_URL . $path;
    }

    /**
     * Rediriger vers une URL
     * 
     * @param string $path
     * @param int $statusCode
     * @return void
     */
    public static function redirect(string $path, int $statusCode = 302): void
    {
        $url = self::url($path);
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * Rediriger en arrière (page précédente)
     * 
     * @return void
     */
    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
        header("Location: {$referer}");
        exit;
    }
}