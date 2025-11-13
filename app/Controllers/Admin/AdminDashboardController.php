<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Models\User;
use App\Models\Ville;
use App\Models\Quartier;
use App\Models\Coupure;
use App\Models\Signalement;

class AdminDashboardController extends BaseController
{
    private User $userModel;
    private Ville $villeModel;
    private Quartier $quartierModel;
    private Coupure $coupureModel;
    private Signalement $signalementModel;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->villeModel = new Ville();
        $this->quartierModel = new Quartier();
        $this->coupureModel = new Coupure();
        $this->signalementModel = new Signalement();
    }
    
    /**
     * Dashboard admin
     */
    public function index(Request $request): void
    {
        $this->requireAdmin();
        
        // Statistiques
        $stats = [
            'nb_users' => $this->userModel->count(),
            'nb_villes' => $this->villeModel->count(),
            'nb_quartiers' => $this->quartierModel->count(),
            'nb_coupures' => $this->coupureModel->count(),
            'nb_signalements' => $this->signalementModel->count(),
            'nb_signalements_attente' => $this->signalementModel->countByStatus('signale'),
        ];
        
        // Signalements récents
        $signalements = $this->signalementModel->getAllWithDetails();
        $signalements = array_slice($signalements, 0, 10); // 10 derniers
        
        // Coupures actives
        $allCoupures = $this->coupureModel->getAllWithDetails();
        $coupures = array_filter($allCoupures, fn($c) => $c->statut !== 'termine');
        $coupures = array_slice($coupures, 0, 10);
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'signalements' => $signalements,
            'coupures' => $coupures
        ], 'admin');
    }
}