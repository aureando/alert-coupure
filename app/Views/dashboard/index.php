<?php $title = 'Tableau de bord'; ?>

<div class="row mb-4">
    <div class="col">
        <h2 class="mb-3">
            <i class="bi bi-grid-3x3-gap text-primary me-2"></i>
            Bienvenue, <?= e($user['prenom']) ?> !
        </h2>
        <p class="text-muted">Voici un aperçu de votre espace personnel</p>
    </div>
</div>

<!-- Statistiques -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Coupures actives</h6>
                        <h2 class="mb-0 fw-bold text-warning"><?= $stats['nb_coupures_actives'] ?></h2>
                    </div>
                    <i class="bi bi-lightning-charge fs-1 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Mes signalements</h6>
                        <h2 class="mb-0 fw-bold text-info"><?= $stats['nb_signalements_total'] ?></h2>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-1 text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small mb-1">Problèmes résolus</h6>
                        <h2 class="mb-0 fw-bold text-success"><?= $stats['nb_signalements_traites'] ?></h2>
                    </div>
                    <i class="bi bi-check-circle fs-1 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Coupures actives (NOTIFICATION) -->
<div id="coupures" class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-bell-fill text-warning me-2"></i>
            Coupures actives dans votre quartier
        </h4>
    </div>
    
    <?php if (empty($coupures)): ?>
        <div class="alert alert-success border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-3 me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">Aucune coupure prévue !</h5>
                    <p class="mb-0 small">Il n'y a actuellement aucune coupure planifiée dans votre quartier.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($coupures as $coupure): ?>
                <div class="col-12">
                    <div class="card border-warning shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="me-3 fs-3">
                                            <?= service_icon($coupure->type_service) ?>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 fw-bold"><?= e($coupure->ville_nom) ?></h5>
                                            <?php if ($coupure->motif): ?>
                                                <span class="badge bg-secondary mb-2"><?= e($coupure->motif) ?></span>
                                            <?php endif; ?>
                                            <?php if ($coupure->description): ?>
                                                <p class="mb-0 small text-muted"><?= e($coupure->description) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="small">
                                        <div class="mb-1">
                                            <i class="bi bi-calendar-event me-1 text-muted"></i>
                                            <strong>Début :</strong> <?= format_datetime($coupure->date_debut) ?>
                                        </div>
                                        <div class="mb-2">
                                            <i class="bi bi-calendar-check me-1 text-muted"></i>
                                            <strong>Fin :</strong> <?= format_datetime($coupure->date_fin) ?>
                                        </div>
                                        <?= status_badge($coupure->statut) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Mes signalements récents -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-list-ul text-primary me-2"></i>
            Mes signalements récents
        </h4>
        <a href="<?= url('/signalements') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-eye me-1"></i> Voir tout
        </a>
    </div>
    
    <?php if (empty($signalements)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                <p class="text-muted mb-3">Vous n'avez pas encore de signalement</p>
                <a href="<?= url('/signalements/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Créer un signalement
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Problème</th>
                                <th>Description</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($signalements as $sig): ?>
                                <tr>
                                    <td><?= service_icon($sig->type_service) ?></td>
                                    <td><small><?= e($sig->type_probleme) ?></small></td>
                                    <td class="small"><?= e(truncate($sig->description, 50)) ?></td>
                                    <td><?= status_badge($sig->statut) ?></td>
                                    <td class="small text-muted"><?= time_ago($sig->created_at) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <a href="<?= url('/signalements/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Nouveau signalement
            </a>
        </div>
    <?php endif; ?>
</div>