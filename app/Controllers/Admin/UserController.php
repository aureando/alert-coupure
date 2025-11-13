<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Models\User;

class UserController extends BaseController
{
    private User $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    /**
     * Liste des utilisateurs
     */
    public function index(Request $request): void
    {
        $this->requireAdmin();
        
        $users = $this->userModel->getAllWithLocation();
        
        $this->view('admin/users/index', [
            'users' => $users
        ], 'admin');
    }
    
    /**
     * Détail utilisateur
     */
    public function show(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        $users = $this->userModel->getAllWithLocation();
        
        $user = null;
        foreach ($users as $u) {
            if ($u->id == $id) {
                $user = $u;
                break;
            }
        }
        
        if (!$user) {
            $this->redirectWith('/admin/users', 'error', 'Utilisateur introuvable.');
        }
        
        $this->view('admin/users/show', ['user' => $user], 'admin');
    }
    
    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleStatus(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->redirectWith('/admin/users', 'error', 'Utilisateur introuvable.');
        }
        
        $newStatus = !$user->is_active;
        $success = $this->userModel->update($id, ['is_active' => $newStatus]);
        
        if ($success) {
            $message = $newStatus ? 'Utilisateur activé !' : 'Utilisateur désactivé !';
            $this->redirectWith('/admin/users', 'success', $message);
        } else {
            $this->redirectWith('/admin/users', 'error', 'Erreur.');
        }
    }
}