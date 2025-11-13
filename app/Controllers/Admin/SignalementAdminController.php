<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Models\Signalement;

class SignalementAdminController extends BaseController
{
    private Signalement $signalementModel;
    
    public function __construct()
    {
        $this->signalementModel = new Signalement();
    }
    
    /**
     * Liste de tous les signalements
     */
    public function index(Request $request): void
    {
        $this->requireAdmin();
        
        $signalements = $this->signalementModel->getAllWithDetails();
        
        $this->view('admin/signalements/index', [
            'signalements' => $signalements
        ], 'admin');
    }
    
    /**
     * Détail d'un signalement
     */
    public function show(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        $signalements = $this->signalementModel->getAllWithDetails();
        
        $signalement = null;
        foreach ($signalements as $sig) {
            if ($sig->id == $id) {
                $signalement = $sig;
                break;
            }
        }
        
        if (!$signalement) {
            $this->redirectWith('/admin/signalements', 'error', 'Signalement introuvable.');
        }
        
        $this->view('admin/signalements/show', [
            'signalement' => $signalement
        ], 'admin');
    }
    
    /**
     * Changer le statut d'un signalement
     */
    public function updateStatus(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        $statut = $request->post('statut');
        
        if (!in_array($statut, ['signale', 'en_traitement', 'resolu'])) {
            $this->redirectWith('/admin/signalements', 'error', 'Statut invalide.');
        }
        
        $success = $this->signalementModel->update($id, ['statut' => $statut]);
        
        if ($success) {
            $this->redirectWith("/admin/signalements/{$id}", 'success', 'Statut mis à jour !');
        } else {
            $this->redirectWith("/admin/signalements/{$id}", 'error', 'Erreur.');
        }
    }
}