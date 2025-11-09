<?php

namespace App\Core;

/**
 * Classe Session
 * Gestion des sessions utilisateur (login, messages flash, etc.)
 */
class Session
{
    /**
     * Démarrer la session si elle n'est pas déjà démarrée
     * 
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            
            // Options de sécurité pour la session
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path' => '/',
                'domain' => '',
                'secure' => false, // Mettre true en HTTPS
                'httponly' => true, // Protection XSS
                'samesite' => 'Lax' // Protection CSRF
            ]);
            
            session_start();
        }
    }

    /**
     * Définir une valeur dans la session
     * 
     * @param string $key Clé
     * @param mixed $value Valeur
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Récupérer une valeur de la session
     * 
     * @param string $key Clé
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Vérifier si une clé existe dans la session
     * 
     * @param string $key Clé
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Supprimer une valeur de la session
     * 
     * @param string $key Clé
     * @return void
     */
    public static function delete(string $key): void
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Détruire complètement la session
     * 
     * @return void
     */
    public static function destroy(): void
    {
        self::start();
        
        // Vider le tableau de session
        $_SESSION = [];
        
        // Supprimer le cookie de session
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        // Détruire la session
        session_destroy();
    }

    /**
     * Régénérer l'ID de session (sécurité)
     * À utiliser après login pour éviter le session fixation
     * 
     * @return void
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    // ============================================
    // GESTION DE L'UTILISATEUR CONNECTÉ
    // ============================================

    /**
     * Connecter un utilisateur
     * 
     * @param object $user Objet utilisateur
     * @return void
     */
    public static function login(object $user): void
    {
        self::start();
        self::regenerate(); // Sécurité
        
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['user_nom'] = $user->nom;
        $_SESSION['user_prenom'] = $user->prenom;
        $_SESSION['logged_in'] = true;
    }

    /**
     * Déconnecter l'utilisateur
     * 
     * @return void
     */
    public static function logout(): void
    {
        self::destroy();
    }

    /**
     * Vérifier si un utilisateur est connecté
     * 
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        self::start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Obtenir l'ID de l'utilisateur connecté
     * 
     * @return int|null
     */
    public static function getUserId(): ?int
    {
        self::start();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Obtenir le rôle de l'utilisateur connecté
     * 
     * @return string|null
     */
    public static function getUserRole(): ?string
    {
        self::start();
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Vérifier si l'utilisateur est admin
     * 
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return self::getUserRole() === ROLE_ADMIN;
    }

    /**
     * Obtenir toutes les infos de l'utilisateur connecté
     * 
     * @return array|null
     */
    public static function getUser(): ?array
    {
        self::start();
        
        if (!self::isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'role' => $_SESSION['user_role'] ?? null,
            'nom' => $_SESSION['user_nom'] ?? null,
            'prenom' => $_SESSION['user_prenom'] ?? null,
        ];
    }

    // ============================================
    // MESSAGES FLASH (notifications temporaires)
    // ============================================

    /**
     * Définir un message flash
     * 
     * @param string $type Type de message (success, error, warning, info)
     * @param string $message Message à afficher
     * @return void
     */
    public static function setFlash(string $type, string $message): void
    {
        self::start();
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Récupérer et supprimer un message flash
     * 
     * @param string $type Type de message
     * @return string|null
     */
    public static function getFlash(string $type): ?string
    {
        self::start();
        
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        
        return null;
    }

    /**
     * Vérifier si un message flash existe
     * 
     * @param string $type Type de message
     * @return bool
     */
    public static function hasFlash(string $type): bool
    {
        self::start();
        return isset($_SESSION['flash'][$type]);
    }

    /**
     * Récupérer tous les messages flash et les supprimer
     * 
     * @return array
     */
    public static function getAllFlash(): array
    {
        self::start();
        
        $flashes = $_SESSION['flash'] ?? [];
        
        // Supprimer tous les messages flash
        unset($_SESSION['flash']);
        
        return $flashes;
    }

    // ============================================
    // PROTECTION CSRF (optionnel mais recommandé)
    // ============================================

    /**
     * Générer un token CSRF
     * 
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        self::start();
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifier un token CSRF
     * 
     * @param string $token Token à vérifier
     * @return bool
     */
    public static function verifyCsrfToken(string $token): bool
    {
        self::start();
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}