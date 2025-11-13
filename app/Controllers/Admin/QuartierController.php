<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Quartier;
use App\Models\Ville;

class QuartierController extends BaseController
{
    private Quartier $quartierModel;
    private Ville $villeModel;
    
    public function __construct()
    {
        $this->quartierModel = new Quartier();
        $this->villeModel = new Ville();
    }
    
    /**
     * Liste des quartiers
     */
    public function index(Request $request): void
    {
        $this->requireAdmin();
        
        $quartiers = $this->quartierModel->getAllWithVille();
        $villes = $this->villeModel->all('nom ASC');
        
        $this->view('admin/quartiers/index', [
            'quartiers' => $quartiers,
            'villes' => $villes
        ], 'admin');
    }
    
    /**
     * Formulaire création
     */
    public function create(Request $request): void
    {
        $this->requireAdmin();
        
        $villes = $this->villeModel->all('nom ASC');
        $this->view('admin/quartiers/create', ['villes' => $villes], 'admin');
    }
    
    /**
     * Enregistrer
     */
    public function store(Request $request): void
    {
        $this->requireAdmin();
        
        $validator = new Validator($request->getAllPost());
        $validator->setRules([
            'nom' => 'required|validName',
            'ville_id' => 'required|exists:ac_villes,id'
        ]);
        
        if ($validator->fails()) {
            $this->redirectWith('/admin/quartiers/create', 'error', $validator->getErrorsAsString());
        }
        
        $id = $this->quartierModel->create([
            'nom' => $request->post('nom'),
            'ville_id' => $request->post('ville_id')
        ]);
        
        if ($id) {
            $this->redirectWith('/admin/quartiers', 'success', 'Quartier créé avec succès !');
        } else {
            $this->redirectWith('/admin/quartiers/create', 'error', 'Erreur lors de la création.');
        }
    }
    
    /**
     * Formulaire édition
     */
    public function edit(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        $quartier = $this->quartierModel->find($id);
        
        if (!$quartier) {
            $this->redirectWith('/admin/quartiers', 'error', 'Quartier introuvable.');
        }
        
        $villes = $this->villeModel->all('nom ASC');
        
        $this->view('admin/quartiers/edit', [
            'quartier' => $quartier,
            'villes' => $villes
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
            'nom' => 'required|validName',
            'ville_id' => 'required|exists:ac_villes,id'
        ]);
        
        if ($validator->fails()) {
            $this->redirectWith("/admin/quartiers/{$id}/edit", 'error', $validator->getErrorsAsString());
        }
        
        $success = $this->quartierModel->update($id, [
            'nom' => $request->post('nom'),
            'ville_id' => $request->post('ville_id')
        ]);
        
        if ($success) {
            $this->redirectWith('/admin/quartiers', 'success', 'Quartier mis à jour !');
        } else {
            $this->redirectWith("/admin/quartiers/{$id}/edit", 'error', 'Erreur.');
        }
    }
    
    /**
     * Supprimer
     */
    public function delete(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        
        if ($this->quartierModel->delete($id)) {
            $this->redirectWith('/admin/quartiers', 'success', 'Quartier supprimé !');
        } else {
            $this->redirectWith('/admin/quartiers', 'error', 'Erreur lors de la suppression.');
        }
    }
}