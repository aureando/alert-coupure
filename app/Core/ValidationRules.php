<?php

namespace App\Core;

use App\Config\Database;
use PDO;

/**
 * Classe ValidationRules
 * Contient toutes les règles de validation réutilisables
 */
class ValidationRules
{
    /**
     * Vérifie qu'un champ est obligatoire (non vide)
     * 
     * @param mixed $value
     * @return bool
     */
    public static function required($value): bool
    {
        if (is_null($value)) {
            return false;
        }
        
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        
        if (is_array($value) && count($value) === 0) {
            return false;
        }
        
        return true;
    }

    /**
     * Vérifie qu'un email est valide
     * 
     * @param string $value
     * @return bool
     */
    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Vérifie la longueur minimale
     * 
     * @param string $value
     * @param int $min
     * @return bool
     */
    public static function min(string $value, int $min): bool
    {
        return mb_strlen($value) >= $min;
    }

    /**
     * Vérifie la longueur maximale
     * 
     * @param string $value
     * @param int $max
     * @return bool
     */
    public static function max(string $value, int $max): bool
    {
        return mb_strlen($value) <= $max;
    }

    /**
     * Vérifie que la longueur est entre min et max
     * 
     * @param string $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    public static function between(string $value, int $min, int $max): bool
    {
        $length = mb_strlen($value);
        return $length >= $min && $length <= $max;
    }

    /**
     * Vérifie que la valeur est numérique
     * 
     * @param mixed $value
     * @return bool
     */
    public static function numeric($value): bool
    {
        return is_numeric($value);
    }

    /**
     * Vérifie que la valeur est un entier
     * 
     * @param mixed $value
     * @return bool
     */
    public static function integer($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Vérifie que la valeur est une URL valide
     * 
     * @param string $value
     * @return bool
     */
    public static function url(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Vérifie que la valeur est alphanumérique
     * 
     * @param string $value
     * @return bool
     */
    public static function alphanumeric(string $value): bool
    {
        return ctype_alnum($value);
    }

    /**
     * Vérifie que la valeur contient uniquement des lettres
     * 
     * @param string $value
     * @return bool
     */
    public static function alpha(string $value): bool
    {
        return ctype_alpha($value);
    }

    /**
     * Vérifie que deux champs correspondent (ex: password et confirmation)
     * 
     * @param mixed $value
     * @param mixed $matchValue
     * @return bool
     */
    public static function match($value, $matchValue): bool
    {
        return $value === $matchValue;
    }

    /**
     * Vérifie qu'une valeur est dans une liste donnée (ENUM)
     * 
     * @param mixed $value
     * @param array $allowedValues
     * @return bool
     */
    public static function in($value, array $allowedValues): bool
    {
        return in_array($value, $allowedValues, true);
    }

    /**
     * Vérifie qu'une valeur n'est PAS dans une liste
     * 
     * @param mixed $value
     * @param array $forbiddenValues
     * @return bool
     */
    public static function notIn($value, array $forbiddenValues): bool
    {
        return !in_array($value, $forbiddenValues, true);
    }

    /**
     * Vérifie qu'une valeur est unique dans une table de BDD
     * 
     * @param mixed $value
     * @param string $table Nom de la table
     * @param string $column Nom de la colonne
     * @param int|null $exceptId ID à exclure (pour update)
     * @return bool
     */
    public static function unique($value, string $table, string $column, ?int $exceptId = null): bool
    {
        try {
            $pdo = Database::getInstance();
            
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = :value";
            
            // Si on exclut un ID (cas de l'update)
            if ($exceptId !== null) {
                $sql .= " AND id != :except_id";
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':value', $value);
            
            if ($exceptId !== null) {
                $stmt->bindValue(':except_id', $exceptId, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $count = $stmt->fetchColumn();
            
            // Retourne true si la valeur n'existe PAS (unique)
            return $count == 0;
            
        } catch (\PDOException $e) {
            error_log('[ValidationRules] Erreur unique: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie qu'une valeur existe dans une table de BDD
     * 
     * @param mixed $value
     * @param string $table Nom de la table
     * @param string $column Nom de la colonne
     * @return bool
     */
    public static function exists($value, string $table, string $column = 'id'): bool
    {
        try {
            $pdo = Database::getInstance();
            
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = :value";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':value', $value);
            $stmt->execute();
            
            $count = $stmt->fetchColumn();
            
            // Retourne true si la valeur existe
            return $count > 0;
            
        } catch (\PDOException $e) {
            error_log('[ValidationRules] Erreur exists: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie qu'une date est valide
     * 
     * @param string $value
     * @param string $format Format de date (Y-m-d par défaut)
     * @return bool
     */
    public static function date(string $value, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $value);
        return $d && $d->format($format) === $value;
    }

    /**
     * Vérifie qu'une date est après une autre
     * 
     * @param string $value Date à vérifier
     * @param string $afterDate Date de référence
     * @return bool
     */
    public static function after(string $value, string $afterDate): bool
    {
        return strtotime($value) > strtotime($afterDate);
    }

    /**
     * Vérifie qu'une date est avant une autre
     * 
     * @param string $value Date à vérifier
     * @param string $beforeDate Date de référence
     * @return bool
     */
    public static function before(string $value, string $beforeDate): bool
    {
        return strtotime($value) < strtotime($beforeDate);
    }

    /**
     * Vérifie qu'un fichier est une image valide
     * 
     * @param array $file Tableau $_FILES
     * @return bool
     */
    public static function image(array $file): bool
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        
        // Vérifier le MIME type
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedMimes)) {
            return false;
        }
        
        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($extension, ALLOWED_IMAGE_EXTENSIONS);
    }

    /**
     * Vérifie la taille maximale d'un fichier (en octets)
     * 
     * @param array $file Tableau $_FILES
     * @param int $maxSize Taille max en octets
     * @return bool
     */
    public static function fileSize(array $file, int $maxSize = MAX_FILE_SIZE): bool
    {
        if (!isset($file['size'])) {
            return false;
        }
        
        return $file['size'] <= $maxSize;
    }

    /**
     * Vérifie qu'un mot de passe est sécurisé
     * Au moins : 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial
     * 
     * @param string $value
     * @return bool
     */
    public static function strongPassword(string $value): bool
    {
        // Au moins 8 caractères
        if (mb_strlen($value) < 8) {
            return false;
        }
        
        // Au moins une minuscule
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }
        
        // Au moins une majuscule
        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }
        
        // Au moins un chiffre
        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }
        
        // Au moins un caractère spécial
        if (!preg_match('/[\W_]/', $value)) {
            return false;
        }
        
        return true;
    }

    /**
     * Vérifie un numéro de téléphone malgache
     * Format: 03X XX XXX XX ou +261 3X XX XXX XX
     * 
     * @param string $value
     * @return bool
     */
    public static function phoneNumber(string $value): bool
    {
        // Retirer les espaces
        $phone = str_replace(' ', '', $value);
        
        // Format: 03XXXXXXXX (10 chiffres)
        if (preg_match('/^03[0-9]{8}$/', $phone)) {
            return true;
        }
        
        // Format: +2613XXXXXXXX
        if (preg_match('/^\+2613[0-9]{8}$/', $phone)) {
            return true;
        }
        
        return false;
    }

    /**
     * Vérifie une valeur avec une regex personnalisée
     * 
     * @param string $value
     * @param string $pattern Pattern regex
     * @return bool
     */
    public static function regex(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }

    /**
     * Vérifie qu'un nom n'a pas plus de 2 voyelles successives
     * Exemple: "Aiiina" sera rejeté, "Rakoto" sera accepté
     * 
     * @param string $value
     * @return bool
     */
    public static function validName(string $value): bool
    {
        // Pas de chiffres
        if (preg_match('/[0-9]/', $value)) {
            return false;
        }
        
        // Pas plus de 2 voyelles consécutives
        if (preg_match('/[aeiouyAEIOUY]{3,}/', $value)) {
            return false;
        }
        
        // Longueur raisonnable (2-50 caractères)
        $length = mb_strlen($value);
        if ($length < 2 || $length > 50) {
            return false;
        }
        
        // Seulement lettres, espaces, tirets et apostrophes
        if (!preg_match('/^[a-zA-ZÀ-ÿ\s\-\']+$/', $value)) {
            return false;
        }
        
        return true;
    }
}