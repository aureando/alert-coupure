<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Coupure;
use App\Models\Ville;
use App\Models\Quartier;

class CoupureController extends BaseController
{
    private Coupure $coupureModel;
    private Ville $villeModel;
    private Quartier $quartierModel;
    
    public function __construct()
    {
        $this->coupureModel = new Coupure();
        $this->villeModel = new Ville();
        $this->quartierModel = new Quartier();
    }
    
    /**
     * Liste des coupures
     */
    public function index(Request $request): void
    {
        $this->requireAdmin();
        
        $coupures = $this->coupureModel->getAllWithDetails();
        
        $this->view('admin/coupures/index', [
            'coupures' => $coupures
        ], 'admin');
    }
    
    /**
     * Formulaire création
     */
    public function create(Request $request): void
    {
        $this->requireAdmin();
        
        // Récupérer la ville de l'admin
        $user = auth_user();
        $villeId = $user['ville_id'] ?? null;
        
        if (!$villeId) {
            $this->redirectWith('/admin/coupures', 'error', 'Vous devez être assigné à une ville pour créer des coupures.');
        }
        
        $ville = $this->villeModel->find($villeId);
        $quartiers = $this->quartierModel->getByVille($villeId);
        
        $this->view('admin/coupures/create', [
            'ville' => $ville,
            'quartiers' => $quartiers
        ], 'admin');
        
    }
    
    /**
     * Enregistrer
     */
    public function store(Request $request): void
    {
        $this->requireAdmin();
        
        $validator = new Validator($request->getAllPost());
        $validator->setRules([
            'type_service' => 'required|in:electricite,eau,les_deux',
            'date_debut' => 'required',
            'date_fin' => 'required',
            'motif' => 'required',
            'description' => 'required|min:10'
        ]);
        
        if ($validator->fails()) {
            $this->redirectWith('/admin/coupures/create', 'error', $validator->getErrorsAsString());
        }
        
        // Récupérer les quartiers sélectionnés
        $quartierIds = $request->post('quartiers') ?? [];
        
        if (empty($quartierIds)) {
            $this->redirectWith('/admin/coupures/create', 'error', 'Veuillez sélectionner au moins un quartier.');
        }
        
        // Créer la coupure
        $coupureId = $this->coupureModel->createWithQuartiers([
            'type_service' => $request->post('type_service'),
            'date_debut' => $request->post('date_debut'),
            'date_fin' => $request->post('date_fin'),
            'motif' => $request->post('motif'),
            'description' => $request->post('description'),
            'statut' => 'planifie',
            'created_by' => auth_id()
        ], $quartierIds);
        
        if ($coupureId) {
            $this->redirectWith('/admin/coupures', 'success', 'Coupure planifiée avec succès !');
        } else {
            $this->redirectWith('/admin/coupures/create', 'error', 'Erreur lors de la création.');
        }
    }
    
    /**
     * Formulaire édition
     */
    public function edit(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        $coupure = $this->coupureModel->find($id);
        
        if (!$coupure) {
            $this->redirectWith('/admin/coupures', 'error', 'Coupure introuvable.');
        }
        
        $user = auth_user();
        $villeId = $user['ville_id'] ?? null;
        $ville = $this->villeModel->find($villeId);
        $quartiers = $this->quartierModel->getByVille($villeId);
        $quartiersSelected = $this->coupureModel->getQuartiers($id);
        $selectedIds = array_column($quartiersSelected, 'id');
        
        $this->view('admin/coupures/edit', [
            'coupure' => $coupure,
            'ville' => $ville,
            'quartiers' => $quartiers,
            'selectedIds' => $selectedIds
        ], 'admin');
    }
    
    /**
     * Mettre à jour
     */
    public function update(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        
        $validator = new Validator($request->getAllPost());
        $validator->setRules([
            'type_service' => 'required|in:electricite,eau,les_deux',
            'date_debut' => 'required',
            'date_fin' => 'required',
            'motif' => 'required',
            'description' => 'required|min:10',
            'statut' => 'required|in:planifie,en_cours,termine'
        ]);
        
        if ($validator->fails()) {
            $this->redirectWith("/admin/coupures/{$id}/edit", 'error', $validator->getErrorsAsString());
        }
        
        $quartierIds = $request->post('quartiers') ?? [];
        
        if (empty($quartierIds)) {
            $this->redirectWith("/admin/coupures/{$id}/edit", 'error', 'Sélectionnez au moins un quartier.');
        }
        
        $success = $this->coupureModel->updateWithQuartiers($id, [
            'type_service' => $request->post('type_service'),
            'date_debut' => $request->post('date_debut'),
            'date_fin' => $request->post('date_fin'),
            'motif' => $request->post('motif'),
            'description' => $request->post('description'),
            'statut' => $request->post('statut')
        ], $quartierIds);
        
        if ($success) {
            $this->redirectWith('/admin/coupures', 'success', 'Coupure mise à jour !');
        } else {
            $this->redirectWith("/admin/coupures/{$id}/edit", 'error', 'Erreur.');
        }
    }
    
    /**
     * Supprimer
     */
    public function delete(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        
        if ($this->coupureModel->delete($id)) {
            $this->redirectWith('/admin/coupures', 'success', 'Coupure supprimée !');
        } else {
            $this->redirectWith('/admin/coupures', 'error', 'Erreur.');
        }
    }
}