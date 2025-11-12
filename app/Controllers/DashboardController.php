<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Coupure;
use App\Models\Signalement;

class DashboardController extends BaseController
{
    private Coupure $coupureModel;
    private Signalement $signalementModel;
    
    public function __construct()
    {
        $this->coupureModel = new Coupure();
        $this->signalementModel = new Signalement();
    }
    
    /**
     * Dashboard utilisateur
     */
    public function index(Request $request): void
    {
        $this->requireAuth();
        
        $user = auth_user();
        $userId = auth_id();
        $quartierId = $user['quartier_id'] ?? null;
        
        // Coupures actives dans le quartier
        $coupures = [];
        if ($quartierId) {
            $coupures = $this->coupureModel->getActiveByQuartier($quartierId);
        }
        
        // Signalements de l'utilisateur (5 derniers)
        $signalements = $this->signalementModel->getByUser($userId);
        $signalementsRecents = array_slice($signalements, 0, 5);
        
        // Statistiques
        $stats = [
            'nb_coupures_actives' => count($coupures),
            'nb_signalements_total' => count($signalements),
            'nb_signalements_attente' => count(array_filter($signalements, fn($s) => $s->statut === 'signale')),
            'nb_signalements_traites' => count(array_filter($signalements, fn($s) => $s->statut === 'resolu'))
        ];
        
        $this->view('dashboard/index', [
            'coupures' => $coupures,
            'signalements' => $signalementsRecents,
            'stats' => $stats,
            'user' => $user
        ]);
    }
}