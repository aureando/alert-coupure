<?php $title = 'Mes signalements'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
        Mes signalements
    </h2>
    <a href="<?= url('/signalements/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        Nouveau signalement
    </a>
</div>

<?php if (empty($signalements)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
            <h5 class="text-muted mb-3">Aucun signalement</h5>
            <p class="text-muted mb-4">Vous n'avez pas encore créé de signalement</p>
            <a href="<?= url('/signalements/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Créer mon premier signalement
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($signalements as $sig): ?>
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">
                                    <?= service_icon($sig->type_service) ?>
                                    <?= e(ucfirst(str_replace('_', ' ', $sig->type_probleme))) ?>
                                </h5>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <?= e($sig->quartier_nom) ?>, <?= e($sig->ville_nom) ?>
                                </p>
                            </div>
                            <?= status_badge($sig->statut) ?>
                        </div>
                        
                        <p class="card-text small">
                            <?= e(truncate($sig->description, 120)) ?>
                        </p>
                        
                        <?php if ($sig->photo): ?>
                            <div class="mb-3">
                                <img src="<?= upload_url('signalements/' . $sig->photo) ?>" 
                                     alt="Photo" 
                                     class="img-thumbnail" 
                                     style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                <?= time_ago($sig->created_at) ?>
                            </small>
                            <a href="<?= url('/signalements/' . $sig->id) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>
                                Voir détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>