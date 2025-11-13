<?php $title = 'Nouvelle Ville'; ?>

<div class="mb-4">
    <a href="<?= url('/admin/villes') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nouvelle Ville
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= url('/admin/villes') ?>">
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de la ville <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" maxlength="10" required>
                        <small class="text-muted">Ex: TNR, TMM, ATB...</small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-check-circle me-2"></i>
                            Créer
                        </button>
                        <a href="<?= url('/admin/villes') ?>" class="btn btn-outline-secondary">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>