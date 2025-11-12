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
        // Récupérer les filtres
        $villeId = $request->get('ville');
        $quartierId = $request->get('quartier');
        $typeService = $request->get('type_service');
        
        // Récupérer les coupures avec filtres
        if ($villeId || $quartierId || $typeService) {
            // Recherche filtrée
            $coupures = $this->coupureModel->search($villeId, $quartierId, $typeService);
        } else {
            // Afficher les coupures récentes par défaut
            $coupures = $this->coupureModel->getRecent(10);
        }
        
        // Récupérer toutes les villes pour la recherche
        $villes = $this->villeModel->all('nom ASC');
        
        // Récupérer les quartiers si une ville est sélectionnée
        $quartiers = [];
        if ($villeId) {
            $quartierModel = new \App\Models\Quartier();
            $quartiers = $quartierModel->getByVille($villeId);
        }
        
        $this->view('home/index', [
            'coupures' => $coupures,
            'villes' => $villes,
            'quartiers' => $quartiers,
            'villeId' => $villeId,
            'quartierId' => $quartierId,
            'typeService' => $typeService
        ]);
    }
}