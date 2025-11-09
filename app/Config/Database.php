<?php

namespace App\Config;

use PDO;
use PDOException;

/**
 * Classe Database
 * Gestion de la connexion à la base de données avec PDO
 * Pattern Singleton pour une seule instance de connexion
 */
class Database
{
    /**
     * Instance unique de la connexion PDO
     * @var PDO|null
     */
    private static ?PDO $instance = null;

    /**
     * Constructeur privé pour empêcher l'instanciation directe
     * (Pattern Singleton)
     */
    private function __construct()
    {
        // Le constructeur est privé
    }

    /**
     * Empêcher le clonage de l'instance
     */
    private function __clone()
    {
        // Ne rien faire
    }

    /**
     * Obtenir l'instance unique de connexion PDO
     * 
     * @return PDO Instance de connexion
     * @throws PDOException Si la connexion échoue
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                // DSN (Data Source Name)
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    DB_HOST,
                    DB_NAME,
                    DB_CHARSET
                );

                // Options PDO pour sécurité et performance
                $options = [
                    // Mode d'erreur : exceptions
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    
                    // Mode de récupération par défaut : objets
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    
                    // Désactiver l'émulation des requêtes préparées (sécurité)
                    PDO::ATTR_EMULATE_PREPARES => false,
                    
                    // Connexion persistante (meilleure performance)
                    PDO::ATTR_PERSISTENT => true,
                    
                    // Convertir les NULL en chaînes vides
                    PDO::ATTR_ORACLE_NULLS => PDO::NULL_TO_STRING,
                    
                    // Timeout de connexion (5 secondes)
                    PDO::ATTR_TIMEOUT => 5,
                ];

                // Créer la connexion
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);

                // Log en mode développement
                if (DEBUG_MODE) {
                    error_log('[Database] Connexion établie avec succès');
                }

            } catch (PDOException $e) {
                // Log de l'erreur
                error_log('[Database ERROR] ' . $e->getMessage());

                // Message différent selon l'environnement
                if (ENVIRONMENT === 'development') {
                    throw new PDOException(
                        'Erreur de connexion à la base de données : ' . $e->getMessage(),
                        (int)$e->getCode()
                    );
                } else {
                    throw new PDOException(
                        'Impossible de se connecter à la base de données. Veuillez réessayer plus tard.',
                        500
                    );
                }
            }
        }

        return self::$instance;
    }

    /**
     * Fermer la connexion (rarement nécessaire avec PDO)
     * 
     * @return void
     */
    public static function closeConnection(): void
    {
        self::$instance = null;

        if (DEBUG_MODE) {
            error_log('[Database] Connexion fermée');
        }
    }

    /**
     * Tester la connexion à la base de données
     * 
     * @return bool True si la connexion fonctionne
     */
    public static function testConnection(): bool
    {
        try {
            $pdo = self::getInstance();
            $pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            error_log('[Database TEST] Échec : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les informations sur la base de données
     * Utile pour debug
     * 
     * @return array Informations de connexion
     */
    public static function getInfo(): array
    {
        try {
            $pdo = self::getInstance();
            
            return [
                'server' => $pdo->getAttribute(PDO::ATTR_SERVER_INFO),
                'client' => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
                'connection' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS),
                'driver' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
            ];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Exécuter une requête SQL simple (sans préparation)
     * ATTENTION : À utiliser UNIQUEMENT pour des requêtes sans paramètres
     * 
     * @param string $sql Requête SQL
     * @return mixed Résultat de la requête
     * @throws PDOException
     */
    public static function query(string $sql)
    {
        try {
            $pdo = self::getInstance();
            return $pdo->query($sql);
        } catch (PDOException $e) {
            error_log('[Database QUERY ERROR] ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Commencer une transaction
     * 
     * @return bool
     */
    public static function beginTransaction(): bool
    {
        try {
            return self::getInstance()->beginTransaction();
        } catch (PDOException $e) {
            error_log('[Database TRANSACTION ERROR] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Valider une transaction
     * 
     * @return bool
     */
    public static function commit(): bool
    {
        try {
            return self::getInstance()->commit();
        } catch (PDOException $e) {
            error_log('[Database COMMIT ERROR] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Annuler une transaction
     * 
     * @return bool
     */
    public static function rollback(): bool
    {
        try {
            return self::getInstance()->rollBack();
        } catch (PDOException $e) {
            error_log('[Database ROLLBACK ERROR] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir l'ID du dernier élément inséré
     * 
     * @return string
     */
    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }
}