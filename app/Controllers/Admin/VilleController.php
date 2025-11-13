<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Ville;

class VilleController extends BaseController
{
    private Ville $villeModel;
    
    public function __construct()
    {
        $this->villeModel = new Ville();
    }
    
    /**
     * Liste des villes
     */
    public function index(Request $request): void
    {
        $this->requireAdmin();
        
        $villes = $this->villeModel->getWithQuartiersCount();
        
        $this->view('admin/villes/index', [
            'villes' => $villes
        ], 'admin');
    }
    
    /**
     * Formulaire création
     */
    public function create(Request $request): void
    {
        $this->requireAdmin();
        $this->view('admin/villes/create', [], 'admin');
    }
    
    /**
     * Enregistrer
     */
    public function store(Request $request): void
    {
        $this->requireAdmin();
        
        $validator = new Validator($request->getAllPost());
        $validator->setRules([
            'nom' => 'required|validName|unique:ac_villes,nom',
            'code' => 'required|alphanumeric|unique:ac_villes,code'
        ]);
        
        if ($validator->fails()) {
            $this->redirectWith('/admin/villes/create', 'error', $validator->getErrorsAsString());
        }
        
        $id = $this->villeModel->create([
            'nom' => $request->post('nom'),
            'code' => strtoupper($request->post('code'))
        ]);
        
        if ($id) {
            $this->redirectWith('/admin/villes', 'success', 'Ville créée avec succès !');
        } else {
            $this->redirectWith('/admin/villes/create', 'error', 'Erreur lors de la création.');
        }
    }
    
    /**
     * Formulaire édition
     */
    public function edit(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        $ville = $this->villeModel->find($id);
        
        if (!$ville) {
            $this->redirectWith('/admin/villes', 'error', 'Ville introuvable.');
        }
        
        $this->view('admin/villes/edit', ['ville' => $ville], 'admin');
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
            'code' => 'required|alphanumeric'
        ]);
        
        if ($validator->fails()) {
            $this->redirectWith("/admin/villes/{$id}/edit", 'error', $validator->getErrorsAsString());
        }
        
        $success = $this->villeModel->update($id, [
            'nom' => $request->post('nom'),
            'code' => strtoupper($request->post('code'))
        ]);
        
        if ($success) {
            $this->redirectWith('/admin/villes', 'success', 'Ville mise à jour !');
        } else {
            $this->redirectWith("/admin/villes/{$id}/edit", 'error', 'Erreur lors de la mise à jour.');
        }
    }
    
    /**
     * Supprimer
     */
    public function delete(Request $request, array $params): void
    {
        $this->requireAdmin();
        
        $id = (int) $params['id'];
        
        if ($this->villeModel->delete($id)) {
            $this->redirectWith('/admin/villes', 'success', 'Ville supprimée !');
        } else {
            $this->redirectWith('/admin/villes', 'error', 'Erreur lors de la suppression.');
        }
    }
}