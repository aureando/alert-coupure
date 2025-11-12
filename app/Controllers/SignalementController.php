<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Validator;
use App\Models\Signalement;
use App\Models\Quartier;

class SignalementController extends BaseController
{
    private Signalement $signalementModel;
    private Quartier $quartierModel;
    
    public function __construct()
    {
        $this->signalementModel = new Signalement();
        $this->quartierModel = new Quartier();
    }
    
    /**
     * Liste des signalements de l'utilisateur
     */
    public function index(Request $request): void
    {
        $this->requireAuth();
        
        $userId = auth_id();
        $signalements = $this->signalementModel->getByUser($userId);
        
        $this->view('signalements/index', [
            'signalements' => $signalements
        ]);
    }
    
    /**
     * Formulaire de création
     */
    public function create(Request $request): void
    {
        $this->requireAuth();
        
        $user = auth_user();
        $quartier = null;
        
        if ($user['quartier_id']) {
            $quartier = $this->quartierModel->find($user['quartier_id']);
        }
        
        $this->view('signalements/create', [
            'quartier' => $quartier
        ]);
    }
    
    /**
     * Enregistrer un signalement
     */
    public function store(Request $request): void
    {
        $this->requireAuth();
        
        $user = auth_user();
        
        // Validation
        $validator = new Validator($request->getAllPost());
        
        $validator->setRules([
            'type_service' => 'required|in:electricite,eau',
            'type_probleme' => 'required',
            'description' => 'required|min:10|max:500'
        ]);
        
        $validator->setLabels([
            'type_service' => 'Type de service',
            'type_probleme' => 'Type de problème',
            'description' => 'Description'
        ]);
        
        // Validation du fichier si présent
        if ($request->hasFile('photo')) {
            $validator->setRules([
                'photo' => 'image|fileSize:' . MAX_FILE_SIZE
            ]);
        }
        
        if ($validator->fails()) {
            $this->redirectWith('/signalements/create', 'error', $validator->getErrorsAsString());
        }
        
        // Traiter l'upload de photo
        $photoName = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $photoName = 'sig_' . uniqid() . '.' . $extension;
            
            if (!move_uploaded_file($file['tmp_name'], SIGNALEMENT_UPLOAD_DIR . '/' . $photoName)) {
                $this->redirectWith('/signalements/create', 'error', 'Erreur lors de l\'upload de la photo.');
            }
        }
        
        // Créer le signalement
        $data = [
            'user_id' => auth_id(),
            'quartier_id' => $user['quartier_id'],
            'type_service' => $request->post('type_service'),
            'type_probleme' => $request->post('type_probleme'),
            'description' => $request->post('description'),
            'photo' => $photoName,
            'statut' => 'signale'
        ];
        
        $id = $this->signalementModel->create($data);
        
        if ($id) {
            $this->redirectWith('/signalements', 'success', 'Votre signalement a été enregistré avec succès !');
        } else {
            $this->redirectWith('/signalements/create', 'error', 'Une erreur est survenue.');
        }
    }
    
    /**
     * Voir un signalement
     */
    public function show(Request $request, array $params): void
    {
        $this->requireAuth();
        
        $id = (int) $params['id'];
        $userId = auth_id();
        
        // Récupérer le signalement
        $signalements = $this->signalementModel->getByUser($userId);
        $signalement = null;
        
        foreach ($signalements as $sig) {
            if ($sig->id == $id) {
                $signalement = $sig;
                break;
            }
        }
        
        if (!$signalement) {
            $this->redirectWith('/signalements', 'error', 'Signalement introuvable.');
        }
        
        $this->view('signalements/show', [
            'signalement' => $signalement
        ]);
    }
}