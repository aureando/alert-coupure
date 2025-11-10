<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Quartier;

class ApiController extends BaseController
{
    private Quartier $quartierModel;
    
    public function __construct()
    {
        $this->quartierModel = new Quartier();
    }
    
    /**
     * Récupérer les quartiers d'une ville (AJAX)
     */
    public function getQuartiersByVille(Request $request, array $params): void
    {
        $villeId = (int) $params['ville_id'];
        
        $quartiers = $this->quartierModel->getByVille($villeId);
        
        $this->json($quartiers);
    }
}