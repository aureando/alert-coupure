<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Coupure;
use App\Models\Ville;

class HomeController extends BaseController
{
    private Coupure $coupureModel;
    private Ville $villeModel;
    
    public function __construct()
    {
        $this->coupureModel = new Coupure();
        $this->villeModel = new Ville();
    }
    
    /**
     * Page d'accueil
     */
    public function index(Request $request): void
    {
        // Récupérer les coupures récentes
        $coupures = $this->coupureModel->getRecent(10);
        
        // Récupérer toutes les villes pour la recherche
        $villes = $this->villeModel->all('nom ASC');
        
        $this->view('home/index', [
            'coupures' => $coupures,
            'villes' => $villes
        ]);
    }
}