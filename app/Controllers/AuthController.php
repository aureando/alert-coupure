<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;
use App\Models\Ville;
use App\Models\Quartier;

class AuthController extends BaseController
{
    private User $userModel;
    private Ville $villeModel;
    private Quartier $quartierModel;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->villeModel = new Ville();
        $this->quartierModel = new Quartier();
    }
    
    /**
     * Afficher la page de connexion
     */
    public function showLogin(Request $request): void
    {
        require_guest();
        $this->view('auth/login');
    }
    
    /**
     * Traiter la connexion
     */
    public function login(Request $request): void
    {
        require_guest();
        
        // Validation
        $validator = new Validator($request->getAllPost());
        $validator->setRules([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if ($validator->fails()) {
            Session::setFlash('error', 'Email ou mot de passe incorrect.');
            redirect('/login');
        }
        
        // Vérifier l'utilisateur
        $user = $this->userModel->findByEmail($request->post('email'));
        
        if (!$user || !$this->userModel->verifyPassword($request->post('password'), $user->password)) {
            Session::setFlash('error', 'Email ou mot de passe incorrect.');
            redirect('/login');
        }
        
        // Vérifier si le compte est actif
        if (!$user->is_active) {
            Session::setFlash('error', 'Votre compte a été désactivé.');
            redirect('/login');
        }
        
        // Connecter l'utilisateur
        Session::login($user);
        
        // Redirection selon le rôle
        if ($user->role === 'admin') {
            $this->redirectWith('/admin', 'success', 'Bienvenue, ' . $user->prenom . ' !');
        } else {
            $this->redirectWith('/dashboard', 'success', 'Connexion réussie !');
        }
    }
    
    /**
     * Afficher la page d'inscription
     */
    public function showRegister(Request $request): void
    {
        require_guest();
        
        $villes = $this->villeModel->all('nom ASC');
        
        $this->view('auth/register', [
            'villes' => $villes
        ]);
    }
    
    /**
     * Traiter l'inscription
     */
    public function register(Request $request): void
    {
        require_guest();
        
        // Validation
        $validator = new Validator($request->getAllPost());
        
        $validator->setRules([
            'nom' => 'required|validName',
            'prenom' => 'required|validName',
            'email' => 'required|email|unique:ac_users,email',
            'password' => 'required|min:8',
            'password_confirm' => 'required|match:password',
            'ville_id' => 'required|exists:ac_villes,id',
            'quartier_id' => 'required|exists:ac_quartiers,id'
        ]);
        
        $validator->setLabels([
            'nom' => 'Nom',
            'prenom' => 'Prénom',
            'email' => 'Email',
            'password' => 'Mot de passe',
            'password_confirm' => 'Confirmation du mot de passe',
            'ville_id' => 'Ville',
            'quartier_id' => 'Quartier'
        ]);
        
        if ($validator->fails()) {
            Session::setFlash('error', $validator->getErrorsAsString());
            redirect('/register');
        }
        
        // Créer l'utilisateur
        $userId = $this->userModel->createUser([
            'nom' => $request->post('nom'),
            'prenom' => $request->post('prenom'),
            'email' => $request->post('email'),
            'password' => $request->post('password'),
            'quartier_id' => $request->post('quartier_id'),
            'role' => 'user'
        ]);
        
        if ($userId) {
            $this->redirectWith('/login', 'success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
        } else {
            $this->redirectWith('/register', 'error', 'Une erreur est survenue lors de l\'inscription.');
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout(Request $request): void
    {
        Session::logout();
        $this->redirectWith('/login', 'success', 'Vous êtes déconnecté.');
    }
}