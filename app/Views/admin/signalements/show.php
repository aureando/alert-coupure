<?php $title = 'Détail Signalement #' . $signalement->id; ?>

<div class="mb-4">
    <a href="<?= url('/admin/signalements') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <?= service_icon($signalement->type_service) ?>
                        Signalement #<?= $signalement->id ?>
                    </h4>
                    <?= status_badge($signalement->statut) ?>
                </div>
            </div>
            <div class="card-body p-4">
                
                <!-- Utilisateur -->
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase small mb-2">Signalé par</h6>
                    <p class="mb-0">
                        <strong><?= e($signalement->user_nom) ?></strong><br>
                        <small class="text-muted">
                            <i class="bi bi-envelope me-1"></i>
                            <?= e($signalement->user_email) ?>
                        </small>
                    </p>
                </div>
                
                <!-- Localisation -->
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase small mb-2">Localisation</h6>
                    <p class="mb-0">
                        <i class="bi bi-geo-alt text-primary me-1"></i>
                        <?= e($signalement->quartier_nom) ?>, <?= e($signalement->ville_nom) ?>
                    </p>
                </div>
                
                <!-- Type de problème -->
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase small mb-2">Type de problème</h6>
                    <p class="mb-0">
                        <strong><?= e(ucfirst(str_replace('_', ' ', $signalement->type_probleme))) ?></strong>
                    </p>
                </div>
                
                <!-- Description -->
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase small mb-2">Description</h6>
                    <div class="border-start border-primary border-3 ps-3">
                        <p class="mb-0"><?= nl2br(e($signalement->description)) ?></p>
                    </div>
                </div>
                
                <!-- Photo -->
                <?php if ($signalement->photo): ?>
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small mb-2">Photo</h6>
                        <img src="<?= upload_url('signalements/' . $signalement->photo) ?>" 
                             alt="Photo" 
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 400px;">
                    </div>
                <?php endif; ?>
                
                <!-- Dates -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted text-uppercase small mb-2">Date de signalement</h6>
                        <p class="mb-0">
                            <?= format_datetime($signalement->created_at) ?><br>
                            <small class="text-muted">(<?= time_ago($signalement->created_at) ?>)</small>
                        </p>
                    </div>
                    <?php if ($signalement->updated_at && $signalement->updated_at !== $signalement->created_at): ?>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-2">Dernière mise à jour</h6>
                            <p class="mb-0">
                                <?= format_datetime($signalement->updated_at) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Changement de statut -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i>
                    Changer le statut
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('/admin/signalements/' . $signalement->id . '/update-status') ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nouveau statut</label>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="statut" 
                                   id="signale" value="signale" 
                                   <?= $signalement->statut === 'signale' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="signale">
                                <?= status_badge('signale') ?>
                            </label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="statut" 
                                   id="en_traitement" value="en_traitement"
                                   <?= $signalement->statut === 'en_traitement' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="en_traitement">
                                <?= status_badge('en_traitement') ?>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="statut" 
                                   id="resolu" value="resolu"
                                   <?= $signalement->statut === 'resolu' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="resolu">
                                <?= status_badge('resolu') ?>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-warning text-dark w-100">
                        <i class="bi bi-check-circle me-2"></i>
                        Mettre à jour
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>