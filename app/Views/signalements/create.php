<?php $title = 'Nouveau signalement'; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="mb-4">
            <a href="<?= url('/signalements') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Signaler un problème
                </h4>
            </div>
            <div class="card-body p-4">
                
                <?php if ($quartier): ?>
                    <div class="alert alert-info border-0 mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Localisation :</strong> <?= e($quartier->nom) ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        Erreur : Aucun quartier associé à votre compte
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= url('/signalements') ?>" enctype="multipart/form-data">
                    
                    <!-- Type de service -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-droplet-fill text-primary me-1"></i>
                            Type de service <span class="text-danger">*</span>
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="radio" name="type_service" 
                                           id="electricite" value="electricite" required>
                                    <label class="form-check-label card p-3" for="electricite">
                                        <i class="bi bi-lightning-charge-fill text-warning fs-3"></i>
                                        <h6 class="mb-0 mt-2">Électricité</h6>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="radio" name="type_service" 
                                           id="eau" value="eau" required>
                                    <label class="form-check-label card p-3" for="eau">
                                        <i class="bi bi-droplet-fill text-primary fs-3"></i>
                                        <h6 class="mb-0 mt-2">Eau</h6>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Type de problème -->
                    <div class="mb-3">
                        <label for="type_probleme" class="form-label fw-semibold">
                            Type de problème <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="type_probleme" name="type_probleme" required>
                            <option value="">-- Choisir --</option>
                            <optgroup label="Électricité">
                                <option value="panne">Panne générale</option>
                                <option value="poteau_casse">Poteau cassé</option>
                                <option value="cable_arrache">Câble arraché</option>
                                <option value="transformateur_hs">Transformateur HS</option>
                            </optgroup>
                            <optgroup label="Eau">
                                <option value="fuite_eau">Fuite d'eau</option>
                                <option value="canalisation_cassee">Canalisation cassée</option>
                                <option value="compteur_defectueux">Compteur défectueux</option>
                            </optgroup>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">
                            Description détaillée <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            class="form-control" 
                            id="description" 
                            name="description" 
                            rows="5"
                            placeholder="Décrivez le problème en détail (localisation précise, gravité, etc.)"
                            required
                            minlength="10"
                            maxlength="500"
                        ></textarea>
                        <small class="text-muted">Minimum 10 caractères, maximum 500</small>
                    </div>
                    
                    <!-- Photo -->
                    <div class="mb-4">
                        <label for="photo" class="form-label fw-semibold">
                            <i class="bi bi-camera me-1"></i>
                            Photo (optionnel)
                        </label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Formats acceptés : JPG, PNG, GIF, WEBP (max 5 Mo)</small>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-send me-2"></i>
                            Envoyer le signalement
                        </button>
                        <a href="<?= url('/signalements') ?>" class="btn btn-outline-secondary">
                            Annuler
                        </a>
                    </div>
                    
                </form>
                
            </div>
        </div>
        
    </div>
</div>

<style>
.form-check-card .form-check-input {
    display: none;
}
.form-check-card .card {
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}
.form-check-card .card:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.form-check-card input:checked + .card {
    border-color: var(--bs-primary);
    background: rgba(13, 110, 253, 0.05);
}
</style>