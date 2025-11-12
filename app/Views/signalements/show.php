<?php $title = 'Détail du signalement'; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="mb-4">
            <a href="<?= url('/signalements') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Retour à mes signalements
            </a>
        </div>
        
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
                
                <!-- Informations -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted text-uppercase small mb-2">Type de problème</h6>
                        <p class="mb-0">
                            <strong><?= e(ucfirst(str_replace('_', ' ', $signalement->type_probleme))) ?></strong>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted text-uppercase small mb-2">Localisation</h6>
                        <p class="mb-0">
                            <i class="bi bi-geo-alt text-primary me-1"></i>
                            <?= e($signalement->quartier_nom) ?>, <?= e($signalement->ville_nom) ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted text-uppercase small mb-2">Date de signalement</h6>
                        <p class="mb-0">
                            <i class="bi bi-calendar text-muted me-1"></i>
                            <?= format_datetime($signalement->created_at) ?>
                            <small class="text-muted">(<?= time_ago($signalement->created_at) ?>)</small>
                        </p>
                    </div>
                    <?php if ($signalement->updated_at && $signalement->updated_at !== $signalement->created_at): ?>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-2">Dernière mise à jour</h6>
                            <p class="mb-0">
                                <i class="bi bi-clock text-muted me-1"></i>
                                <?= format_datetime($signalement->updated_at) ?>
                            </p>
                        </div>
                    <?php endif; ?>
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
                             alt="Photo du signalement" 
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 400px;">
                    </div>
                <?php endif; ?>
                
                <!-- Statut avec explication -->
                <div class="alert <?= match($signalement->statut) {
                    'signale' => 'alert-info',
                    'en_traitement' => 'alert-warning',
                    'resolu' => 'alert-success',
                    default => 'alert-secondary'
                } ?> border-0">
                    <h6 class="alert-heading mb-2">
                        <i class="bi bi-<?= match($signalement->statut) {
                            'signale' => 'info-circle',
                            'en_traitement' => 'hourglass-split',
                            'resolu' => 'check-circle',
                            default => 'question-circle'
                        } ?> me-2"></i>
                        <?= match($signalement->statut) {
                            'signale' => 'Signalement reçu',
                            'en_traitement' => 'En cours de traitement',
                            'resolu' => 'Problème résolu',
                            default => 'Statut inconnu'
                        } ?>
                    </h6>
                    <p class="mb-0 small">
                        <?= match($signalement->statut) {
                            'signale' => 'Votre signalement a été enregistré. Il sera traité dans les plus brefs délais.',
                            'en_traitement' => 'Nos équipes sont en train de traiter votre signalement.',
                            'resolu' => 'Le problème signalé a été résolu. Merci de votre contribution !',
                            default => ''
                        } ?>
                    </p>
                </div>
                
            </div>
        </div>
        
    </div>
</div>