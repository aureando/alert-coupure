<?php

/**
 * Fonctions d'authentification essentielles
 */

use App\Core\Session;

/**
 * Vérifier si un utilisateur est connecté
 * 
 * @return bool
 */
function is_logged_in(): bool
{
    return Session::isLoggedIn();
}

/**
 * Vérifier si l'utilisateur est admin
 * 
 * @return bool
 */
function is_admin(): bool
{
    return Session::isAdmin();
}

/**
 * Obtenir l'utilisateur connecté
 * 
 * @return array|null
 */
function auth_user(): ?array
{
    return Session::getUser();
}

/**
 * Obtenir l'ID de l'utilisateur connecté
 * 
 * @return int|null
 */
function auth_id(): ?int
{
    return Session::getUserId();
}

/**
 * Exiger une authentification (ou rediriger)
 * 
 * @param string $redirectTo
 * @return void
 */
function require_auth(string $redirectTo = '/login'): void
{
    if (!is_logged_in()) {
        Session::setFlash('error', 'Vous devez être connecté pour accéder à cette page.');
        redirect($redirectTo);
    }
}

/**
 * Exiger un rôle admin (ou rediriger)
 * 
 * @param string $redirectTo
 * @return void
 */
function require_admin(string $redirectTo = '/'): void
{
    require_auth();
    
    if (!is_admin()) {
        Session::setFlash('error', 'Accès refusé. Vous devez être administrateur.');
        redirect($redirectTo);
    }
}

/**
 * Exiger d'être déconnecté (pour pages login/register)
 * 
 * @param string $redirectTo
 * @return void
 */
function require_guest(string $redirectTo = '/dashboard'): void
{
    if (is_logged_in()) {
        redirect($redirectTo);
    }
}