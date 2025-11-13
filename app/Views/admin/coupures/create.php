<?php $title = 'Planifier une Coupure'; ?>

<div class="mb-4">
    <a href="<?= url('/admin/coupures') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="bi bi-lightning-charge me-2"></i>
                    Planifier une Coupure
                </h4>
            </div>
            <div class="card-body p-4">
                
                <div class="alert alert-info border-0 mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Votre ville :</strong> <?= e($ville->nom) ?>
                </div>
                
                <form method="POST" action="<?= url('/admin/coupures') ?>">
                    
                    <!-- Type de service -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Type de service <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_service" 
                                           id="electricite" value="electricite" required>
                                    <label class="form-check-label" for="electricite">
                                        <i class="bi bi-lightning-charge text-warning me-1"></i>
                                        Électricité
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_service" 
                                           id="eau" value="eau" required>
                                    <label class="form-check-label" for="eau">
                                        <i class="bi bi-droplet text-primary me-1"></i>
                                        Eau
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_service" 
                                           id="les_deux" value="les_deux" required>
                                    <label class="form-check-label" for="les_deux">
                                        <i class="bi bi-lightning-charge text-warning me-1"></i>
                                        <i class="bi bi-droplet text-primary me-1"></i>
                                        Les deux
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sélection MULTIPLE des quartiers -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Quartiers concernés <span class="text-danger">*</span>
                        </label>
                        <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label fw-bold" for="selectAll">
                                    <i class="bi bi-check-all me-1"></i>
                                    Sélectionner tous
                                </label>
                            </div>
                            <hr>
                            <?php foreach ($quartiers as $quartier): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input quartier-checkbox" type="checkbox" 
                                           name="quartiers[]" value="<?= $quartier->id ?>" 
                                           id="q<?= $quartier->id ?>">
                                    <label class="form-check-label" for="q<?= $quartier->id ?>">
                                        <?= e($quartier->nom) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Sélectionnez un ou plusieurs quartiers</small>
                    </div>
                    
                    <!-- Dates -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_debut" class="form-label">Date et heure de début <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="date_debut" name="date_debut" required>
                        </div>
                        <div class="col-md-6">
                            <label for="date_fin" class="form-label">Date et heure de fin <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="date_fin" name="date_fin" required>
                        </div>
                    </div>
                    
                    <!-- Motif -->
                    <div class="mb-3">
                        <label for="motif" class="form-label">Motif <span class="text-danger">*</span></label>
                        <select class="form-select" id="motif" name="motif" required>
                            <option value="">-- Choisir --</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Délestage">Délestage</option>
                            <option value="Travaux">Travaux</option>
                            <option value="Urgence">Urgence</option>
                        </select>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning text-dark flex-grow-1">
                            <i class="bi bi-check-circle me-2"></i>
                            Planifier la Coupure
                        </button>
                        <a href="<?= url('/admin/coupures') ?>" class="btn btn-outline-secondary">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Script pour "Sélectionner tous"
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.quartier-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>