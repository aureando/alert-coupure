<?php

/**
 * Fichier : functions.php
 * Fonctions helper générales pour l'application
 */

use App\Core\Session;
use App\Core\Router;

// ============================================
// AFFICHAGE ET FORMATAGE
// ============================================

/**
 * Échapper du HTML (protection XSS)
 * 
 * @param string|null $string
 * @return string
 */
function e(?string $string): string
{
    if ($string === null) {
        return '';
    }
    
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Afficher et échapper du HTML
 * 
 * @param string|null $string
 * @return void
 */
function echo_e(?string $string): void
{
    echo e($string);
}

/**
 * Dumper une variable (debug)
 * 
 * @param mixed $var
 * @return void
 */
function dd(...$vars): void
{
    echo '<pre style="background: #1e1e1e; color: #dcdcdc; padding: 20px; border-radius: 5px; margin: 20px;">';
    
    foreach ($vars as $var) {
        var_dump($var);
        echo "\n\n";
    }
    
    echo '</pre>';
    die();
}

/**
 * Afficher une variable et continuer
 * 
 * @param mixed $var
 * @return void
 */
function dump(...$vars): void
{
    echo '<pre style="background: #1e1e1e; color: #dcdcdc; padding: 20px; border-radius: 5px; margin: 20px;">';
    
    foreach ($vars as $var) {
        var_dump($var);
        echo "\n\n";
    }
    
    echo '</pre>';
}

// ============================================
// URLS ET REDIRECTIONS
// ============================================

/**
 * Générer une URL complète
 * 
 * @param string $path
 * @param array $params
 * @return string
 */
function url(string $path = '', array $params = []): string
{
    return Router::url($path, $params);
}

/**
 * Générer une URL vers un asset
 * 
 * @param string $path
 * @return string
 */
function asset(string $path): string
{
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Générer une URL vers un upload
 * 
 * @param string $path
 * @return string
 */
function upload_url(string $path): string
{
    return UPLOAD_URL . '/' . ltrim($path, '/');
}

/**
 * Rediriger vers une URL
 * 
 * @param string $path
 * @return void
 */
function redirect(string $path): void
{
    Router::redirect($path);
}

/**
 * Rediriger en arrière
 * 
 * @return void
 */
function back(): void
{
    Router::back();
}

/**
 * Rediriger avec message flash
 * 
 * @param string $path
 * @param string $type
 * @param string $message
 * @return void
 */
function redirect_with(string $path, string $type, string $message): void
{
    Session::setFlash($type, $message);
    Router::redirect($path);
}

// ============================================
// MESSAGES FLASH
// ============================================

/**
 * Définir un message flash
 * 
 * @param string $type success|error|warning|info
 * @param string $message
 * @return void
 */
function flash(string $type, string $message): void
{
    Session::setFlash($type, $message);
}

/**
 * Récupérer un message flash
 * 
 * @param string $type
 * @return string|null
 */
function get_flash(string $type): ?string
{
    return Session::getFlash($type);
}

/**
 * Vérifier si un message flash existe
 * 
 * @param string $type
 * @return bool
 */
function has_flash(string $type): bool
{
    return Session::hasFlash($type);
}

/**
 * Afficher un message flash (HTML Bootstrap)
 * 
 * @param string $type
 * @return void
 */
function display_flash(string $type): void
{
    if (has_flash($type)) {
        $message = get_flash($type);
        $alertClass = match($type) {
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-secondary'
        };
        
        echo "<div class='alert {$alertClass} alert-dismissible fade show' role='alert'>";
        echo e($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

/**
 * Afficher tous les messages flash
 * 
 * @return void
 */
function display_all_flash(): void
{
    $types = ['success', 'error', 'warning', 'info'];
    
    foreach ($types as $type) {
        display_flash($type);
    }
}

// ============================================
// FORMATAGE DE DATES
// ============================================

/**
 * Formater une date en français
 * 
 * @param string $date
 * @param string $format
 * @return string
 */
function format_date(string $date, string $format = 'd/m/Y'): string
{
    try {
        $dt = new DateTime($date);
        return $dt->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Formater une date avec heure
 * 
 * @param string $datetime
 * @return string
 */
function format_datetime(string $datetime): string
{
    return format_date($datetime, 'd/m/Y H:i');
}

/**
 * Obtenir le temps relatif (il y a X minutes/heures/jours)
 * 
 * @param string $datetime
 * @return string
 */
function time_ago(string $datetime): string
{
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'À l\'instant';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return "Il y a {$minutes} minute" . ($minutes > 1 ? 's' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "Il y a {$hours} heure" . ($hours > 1 ? 's' : '');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return "Il y a {$days} jour" . ($days > 1 ? 's' : '');
    } else {
        return format_date($datetime);
    }
}

// ============================================
// FORMATAGE DE TEXTE
// ============================================

/**
 * Tronquer un texte
 * 
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Formater un texte multiligne en paragraphes HTML
 * 
 * @param string $text
 * @return string
 */
function nl2p(string $text): string
{
    $paragraphs = explode("\n\n", $text);
    $output = '';
    
    foreach ($paragraphs as $paragraph) {
        if (trim($paragraph)) {
            $output .= '<p>' . nl2br(e($paragraph)) . '</p>';
        }
    }
    
    return $output;
}

// ============================================
// UTILITAIRES
// ============================================

/**
 * Générer un slug à partir d'un texte
 * 
 * @param string $text
 * @return string
 */
function slugify(string $text): string
{
    // Remplacer les caractères spéciaux
    $text = transliterator_transliterate('Any-Latin; Latin-ASCII', $text);
    
    // Convertir en minuscules
    $text = strtolower($text);
    
    // Remplacer les espaces et caractères non alphanumériques par des tirets
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    
    // Supprimer les tirets en début et fin
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Obtenir l'icône Bootstrap selon le type de service
 * 
 * @param string $typeService electricite|eau|les_deux
 * @return string
 */
function service_icon(string $typeService): string
{
    return match($typeService) {
        'electricite' => '<i class="bi bi-lightning-charge text-warning"></i>',
        'eau' => '<i class="bi bi-droplet text-primary"></i>',
        'les_deux' => '<i class="bi bi-lightning-charge text-warning"></i> <i class="bi bi-droplet text-primary"></i>',
        default => '<i class="bi bi-question-circle"></i>'
    };
}

/**
 * Obtenir le badge Bootstrap selon le statut
 * 
 * @param string $statut
 * @return string
 */
function status_badge(string $statut): string
{
    $badges = [
        'planifie' => '<span class="badge bg-primary">Planifié</span>',
        'en_cours' => '<span class="badge bg-warning text-dark">En cours</span>',
        'termine' => '<span class="badge bg-success">Terminé</span>',
        'signale' => '<span class="badge bg-info text-dark">Signalé</span>',
        'en_traitement' => '<span class="badge bg-warning text-dark">En traitement</span>',
        'resolu' => '<span class="badge bg-success">Résolu</span>',
    ];
    
    return $badges[$statut] ?? '<span class="badge bg-secondary">' . e($statut) . '</span>';
}

/**
 * Formater une taille de fichier
 * 
 * @param int $bytes
 * @param int $precision
 * @return string
 */
function format_file_size(int $bytes, int $precision = 2): string
{
    $units = ['o', 'Ko', 'Mo', 'Go'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Vérifier si l'URL actuelle correspond
 * 
 * @param string $path
 * @return bool
 */
function is_active(string $path): bool
{
    $currentPath = $_SERVER['REQUEST_URI'] ?? '';
    return strpos($currentPath, $path) === 0;
}

/**
 * Générer un token CSRF
 * 
 * @return string
 */
function csrf_token(): string
{
    return Session::generateCsrfToken();
}

/**
 * Générer un champ input CSRF
 * 
 * @return string
 */
function csrf_field(): string
{
    $token = csrf_token();
    return "<input type='hidden' name='csrf_token' value='{$token}'>";
}

/**
 * Obtenir la valeur old d'un champ (après validation échouée)
 * 
 * @param string $field
 * @param mixed $default
 * @return mixed
 */
function old(string $field, $default = '')
{
    return Session::get("old_{$field}", $default);
}