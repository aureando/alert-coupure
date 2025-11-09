<?php

namespace App\Core;

/**
 * Classe Request
 * Récupère et nettoie les données des requêtes HTTP (GET, POST, FILES)
 */
class Request
{
    /**
     * Méthode HTTP de la requête (GET, POST, PUT, DELETE...)
     * @var string
     */
    private string $method;

    /**
     * URI de la requête (ex: /dashboard/signalements)
     * @var string
     */
    private string $uri;

    /**
     * Données GET nettoyées
     * @var array
     */
    private array $get = [];

    /**
     * Données POST nettoyées
     * @var array
     */
    private array $post = [];

    /**
     * Fichiers uploadés
     * @var array
     */
    private array $files = [];

    /**
     * Constructeur : récupère et nettoie toutes les données
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $this->getUri();
        $this->get = $this->sanitizeArray($_GET);
        $this->post = $this->sanitizeArray($_POST);
        $this->files = $_FILES;
    }

    /**
     * Récupérer l'URI propre de la requête
     * 
     * @return string
     */
    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Retirer la query string (?param=value)
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Retirer les slashes en trop
        $uri = trim($uri, '/');
        
        return '/' . $uri;
    }
<?php

namespace App\Core;

/**
 * Classe Request
 * Récupère et nettoie les données des requêtes HTTP (GET, POST, FILES)
 */
class Request
{
    /**
     * Méthode HTTP de la requête (GET, POST, PUT, DELETE...)
     * @var string
     */
    private string $method;

    /**
     * URI de la requête (ex: /dashboard/signalements)
     * @var string
     */
    private string $uri;

    /**
     * Données GET nettoyées
     * @var array
     */
    private array $get = [];

    /**
     * Données POST nettoyées
     * @var array
     */
    private array $post = [];

    /**
     * Fichiers uploadés
     * @var array
     */
    private array $files = [];

    /**
     * Constructeur : récupère et nettoie toutes les données
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $this->getUri();
        $this->get = $this->sanitizeArray($_GET);
        $this->post = $this->sanitizeArray($_POST);
        $this->files = $_FILES;
    }

    /**
     * Récupérer l'URI propre de la requête
     * 
     * @return string
     */
    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Retirer la query string (?param=value)
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Retirer les slashes en trop
        $uri = trim($uri, '/');
        
        return '/' . $uri;
    }

    /**
     * Nettoyer une valeur (protection XSS)
     * 
     * @param mixed $value
     * @return mixed
     */
    private function sanitize($value)
    {
        if (is_array($value)) {
            return $this->sanitizeArray($value);
        }
        
        if (is_string($value)) {
            // Nettoyer les balises HTML et les caractères spéciaux
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }

    /**
     * Nettoyer un tableau de valeurs
     * 
     * @param array $array
     * @return array
     */
    private function sanitizeArray(array $array): array
    {
        $cleaned = [];
        
        foreach ($array as $key => $value) {
            $cleaned[$key] = $this->sanitize($value);
        }
        
        return $cleaned;
    }

    // ============================================
    // MÉTHODES PUBLIQUES
    // ============================================

    /**
     * Obtenir la méthode HTTP
     * 
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Obtenir l'URI
     * 
     * @return string
     */
    public function getUriPath(): string
    {
        return $this->uri;
    }

    /**
     * Vérifier si c'est une requête GET
     * 
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    /**
     * Vérifier si c'est une requête POST
     * 
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Vérifier si c'est une requête AJAX
     * 
     * @return bool
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // ============================================
    // RÉCUPÉRATION DES DONNÉES GET
    // ============================================

    /**
     * Récupérer une valeur GET
     * 
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Récupérer toutes les données GET
     * 
     * @return array
     */
    public function getAllGet(): array
    {
        return $this->get;
    }

    /**
     * Vérifier si une clé GET existe
     * 
     * @param string $key
     * @return bool
     */
    public function hasGet(string $key): bool
    {
        return isset($this->get[$key]);
    }

    // ============================================
    // RÉCUPÉRATION DES DONNÉES POST
    // ============================================

    /**
     * Récupérer une valeur POST
     * 
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    public function post(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Récupérer toutes les données POST
     * 
     * @return array
     */
    public function getAllPost(): array
    {
        return $this->post;
    }

    /**
     * Vérifier si une clé POST existe
     * 
     * @param string $key
     * @return bool
     */
    public function hasPost(string $key): bool
    {
        return isset($this->post[$key]);
    }

    // ============================================
    // RÉCUPÉRATION DES FICHIERS
    // ============================================

    /**
     * Récupérer un fichier uploadé
     * 
     * @param string $key Nom du champ file
     * @return array|null
     */
    public function file(string $key): ?array
    {
        if (isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK) {
            return $this->files[$key];
        }
        
        return null;
    }

    /**
     * Récupérer tous les fichiers
     * 
     * @return array
     */
    public function getAllFiles(): array
    {
        return $this->files;
    }

    /**
     * Vérifier si un fichier a été uploadé
     * 
     * @param string $key
     * @return bool
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    // ============================================
    // RÉCUPÉRATION MIXTE (GET ou POST)
    // ============================================

    /**
     * Récupérer une valeur (POST prioritaire, sinon GET)
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    /**
     * Récupérer toutes les données (POST + GET)
     * 
     * @return array
     */
    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    /**
     * Vérifier si une clé existe (GET ou POST)
     * 
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->hasPost($key) || $this->hasGet($key);
    }

    /**
     * Récupérer uniquement certains champs
     * 
     * @param array $keys Liste des clés à récupérer
     * @return array
     */
    public function only(array $keys): array
    {
        $result = [];
        $all = $this->all();
        
        foreach ($keys as $key) {
            if (isset($all[$key])) {
                $result[$key] = $all[$key];
            }
        }
        
        return $result;
    }

    /**
     * Récupérer tout sauf certains champs
     * 
     * @param array $keys Liste des clés à exclure
     * @return array
     */
    public function except(array $keys): array
    {
        $all = $this->all();
        
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        
        return $all;
    }

    // ============================================
    // INFORMATIONS SUR LA REQUÊTE
    // ============================================

    /**
     * Obtenir l'adresse IP du client
     * 
     * @return string
     */
    public function getIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Obtenir le User-Agent
     * 
     * @return string
     */
    public function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Obtenir le referer (page précédente)
     * 
     * @return string|null
     */
    public function getReferer(): ?string
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }
}
    /**
     * Nettoyer une valeur (protection XSS)
     * 
     * @param mixed $value
     * @return mixed
     */
    private function sanitize($value)
    {
        if (is_array($value)) {
            return $this->sanitizeArray($value);
        }
        
        if (is_string($value)) {
            // Nettoyer les balises HTML et les caractères spéciaux
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }

    /**
     * Nettoyer un tableau de valeurs
     * 
     * @param array $array
     * @return array
     */
    private function sanitizeArray(array $array): array
    {
        $cleaned = [];
        
        foreach ($array as $key => $value) {
            $cleaned[$key] = $this->sanitize($value);
        }
        
        return $cleaned;
    }

    // ============================================
    // MÉTHODES PUBLIQUES
    // ============================================

    /**
     * Obtenir la méthode HTTP
     * 
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Obtenir l'URI
     * 
     * @return string
     */
    public function getUriPath(): string
    {
        return $this->uri;
    }

    /**
     * Vérifier si c'est une requête GET
     * 
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    /**
     * Vérifier si c'est une requête POST
     * 
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Vérifier si c'est une requête AJAX
     * 
     * @return bool
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // ============================================
    // RÉCUPÉRATION DES DONNÉES GET
    // ============================================

    /**
     * Récupérer une valeur GET
     * 
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Récupérer toutes les données GET
     * 
     * @return array
     */
    public function getAllGet(): array
    {
        return $this->get;
    }

    /**
     * Vérifier si une clé GET existe
     * 
     * @param string $key
     * @return bool
     */
    public function hasGet(string $key): bool
    {
        return isset($this->get[$key]);
    }

    // ============================================
    // RÉCUPÉRATION DES DONNÉES POST
    // ============================================

    /**
     * Récupérer une valeur POST
     * 
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    public function post(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Récupérer toutes les données POST
     * 
     * @return array
     */
    public function getAllPost(): array
    {
        return $this->post;
    }

    /**
     * Vérifier si une clé POST existe
     * 
     * @param string $key
     * @return bool
     */
    public function hasPost(string $key): bool
    {
        return isset($this->post[$key]);
    }

    // ============================================
    // RÉCUPÉRATION DES FICHIERS
    // ============================================

    /**
     * Récupérer un fichier uploadé
     * 
     * @param string $key Nom du champ file
     * @return array|null
     */
    public function file(string $key): ?array
    {
        if (isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK) {
            return $this->files[$key];
        }
        
        return null;
    }

    /**
     * Récupérer tous les fichiers
     * 
     * @return array
     */
    public function getAllFiles(): array
    {
        return $this->files;
    }

    /**
     * Vérifier si un fichier a été uploadé
     * 
     * @param string $key
     * @return bool
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    // ============================================
    // RÉCUPÉRATION MIXTE (GET ou POST)
    // ============================================

    /**
     * Récupérer une valeur (POST prioritaire, sinon GET)
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    /**
     * Récupérer toutes les données (POST + GET)
     * 
     * @return array
     */
    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    /**
     * Vérifier si une clé existe (GET ou POST)
     * 
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->hasPost($key) || $this->hasGet($key);
    }

    /**
     * Récupérer uniquement certains champs
     * 
     * @param array $keys Liste des clés à récupérer
     * @return array
     */
    public function only(array $keys): array
    {
        $result = [];
        $all = $this->all();
        
        foreach ($keys as $key) {
            if (isset($all[$key])) {
                $result[$key] = $all[$key];
            }
        }
        
        return $result;
    }

    /**
     * Récupérer tout sauf certains champs
     * 
     * @param array $keys Liste des clés à exclure
     * @return array
     */
    public function except(array $keys): array
    {
        $all = $this->all();
        
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        
        return $all;
    }

    // ============================================
    // INFORMATIONS SUR LA REQUÊTE
    // ============================================

    /**
     * Obtenir l'adresse IP du client
     * 
     * @return string
     */
    public function getIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Obtenir le User-Agent
     * 
     * @return string
     */
    public function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Obtenir le referer (page précédente)
     * 
     * @return string|null
     */
    public function getReferer(): ?string
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }
}