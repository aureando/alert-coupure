<?php
/**
 * Fichier de configuration principal
 * Alert Coupure
 */

// ============================================
// ENVIRONNEMENT
// ============================================
define('ENVIRONMENT', 'development'); // 'development' ou 'production'

// Afficher les erreurs en développement
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ============================================
// BASE DE DONNÉES
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'alert_coupure');
define('DB_USER', 'root');
define('DB_PASS', ''); // Ton mot de passe MySQL (vide par défaut avec Laragon)
define('DB_CHARSET', 'utf8mb4');

// ============================================
// CHEMINS DE L'APPLICATION
// ============================================

// Chemin racine du projet
define('ROOT_PATH', dirname(dirname(__DIR__)));

// Chemin du dossier app
define('APP_PATH', ROOT_PATH . '/app');

// Chemin du dossier public
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Chemin des uploads
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// ============================================
// URLs
// ============================================

// URL de base de l'application
// Adapte selon ton installation Laragon
define('BASE_URL', 'http://localhost/alert-coupure/public');

// URL des assets
define('ASSETS_URL', BASE_URL . '/assets');

// URL des uploads
define('UPLOAD_URL', BASE_URL . '/uploads');

// ============================================
// PARAMÈTRES DE L'APPLICATION
// ============================================

// Nom de l'application
define('APP_NAME', 'Alert Coupure');

// Slogan
define('APP_SLOGAN', 'Gestion des coupures d\'eau et d\'électricité à Madagascar');

// Timezone
date_default_timezone_set('Indian/Antananarivo');

// Langue par défaut
define('DEFAULT_LANG', 'fr');

// ============================================
// SESSION
// ============================================

// Nom de la session
define('SESSION_NAME', 'alert_coupure_session');

// Durée de vie de la session (en secondes)
// 2 heures = 7200 secondes
define('SESSION_LIFETIME', 7200);

// ============================================
// SÉCURITÉ
// ============================================

// Clé secrète pour le hashage (CHANGE CETTE VALEUR EN PRODUCTION !)
define('SECRET_KEY', 'alert_coupure_secret_key_2025_change_me');

// Nombre maximum de tentatives de connexion
define('MAX_LOGIN_ATTEMPTS', 5);

// Durée du blocage après tentatives échouées (en minutes)
define('LOGIN_BLOCK_DURATION', 15);

// ============================================
// UPLOAD DE FICHIERS
// ============================================

// Taille maximale des fichiers (en octets)
// 10 MB = 10 * 1024 * 1024
define('MAX_FILE_SIZE', 10 * 1024 * 1024);

// Extensions autorisées pour les photos
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Dossier de stockage des photos de signalements
define('SIGNALEMENT_UPLOAD_DIR', UPLOAD_PATH . '/signalements');

// ============================================
// PAGINATION
// ============================================

// Nombre d'éléments par page
define('ITEMS_PER_PAGE', 10);

// Nombre d'éléments par page (admin)
define('ADMIN_ITEMS_PER_PAGE', 20);

// ============================================
// EMAILS (pour futures fonctionnalités)
// ============================================

// Activer l'envoi d'emails
define('ENABLE_EMAILS', false);

// Email de l'application
define('APP_EMAIL', 'noreply@alertcoupure.mg');

// Nom de l'expéditeur
define('APP_EMAIL_NAME', 'Alert Coupure');

// ============================================
// MESSAGES FLASH (types)
// ============================================

define('FLASH_SUCCESS', 'success');
define('FLASH_ERROR', 'error');
define('FLASH_WARNING', 'warning');
define('FLASH_INFO', 'info');

// ============================================
// STATUTS (pour référence dans le code)
// ============================================

// Statuts des coupures
define('COUPURE_PLANIFIE', 'planifie');
define('COUPURE_EN_COURS', 'en_cours');
define('COUPURE_TERMINE', 'termine');

// Statuts des signalements
define('SIGNALEMENT_SIGNALE', 'signale');
define('SIGNALEMENT_EN_TRAITEMENT', 'en_traitement');
define('SIGNALEMENT_RESOLU', 'resolu');

// Types de service
define('SERVICE_ELECTRICITE', 'electricite');
define('SERVICE_EAU', 'eau');
define('SERVICE_LES_DEUX', 'les_deux');

// Rôles utilisateurs
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');

// ============================================
// MODE DEBUG
// ============================================

// Activer les logs détaillés (uniquement en développement)
define('DEBUG_MODE', ENVIRONMENT === 'development');

// ============================================
// CHARGEMENT DE LA CONFIG LOCALE (optionnel)
// ============================================

// Si tu as des paramètres spécifiques à ton environnement local
// Crée un fichier config.local.php (il sera ignoré par Git)
$localConfig = APP_PATH . '/Config/config.local.php';
if (file_exists($localConfig)) {
    require_once $localConfig;
}