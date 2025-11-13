<?php $title = 'Dashboard Admin'; ?>
<style>
    .stat-cards {
        background: hsla(46, 100%, 90%, 1);
        color: hsla(46, 100%, 50%, 1)
        border-radius: var(--border-radius);
        /* border-top: 2px solid rgba(102, 78, 0, 0.20);
        border-right: 2px solid rgba(102, 78, 0, 0.20);
        border-bottom: 5px solid rgba(102, 78, 0, 0.20);
        border-left: 2px solid rgba(102, 78, 0, 0.20); */
        background: #FFF3CC;
        box-shadow: none;
        padding: 1.5rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-speedometer2 text-primary me-2"></i>
        Tableau de bord
    </h2>
</div>

<!-- Statistiques -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase small mb-1">Utilisateurs</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['nb_users'] ?></h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase small mb-1">Villes</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['nb_villes'] ?></h2>
                    </div>
                    <i class="bi bi-building fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase small mb-1">Quartiers</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['nb_quartiers'] ?></h2>
                    </div>
                    <i class="bi bi-map fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card stat-card info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 text-uppercase small mb-1">Coupures planifiées</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['nb_coupures'] ?></h2>
                    </div>
                    <i class="bi bi-lightning-charge fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card stat-cards">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase small mb-1" style="color: hsla(46, 100%, 20%, 1)">Signalements en attente</h6>
                        <h2 class="mb-0 fw-bold text-warning"><?= $stats['nb_signalements_attente'] ?></h2>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-1 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Signalements récents -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-light">
        <h5 class="mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Signalements récents
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($signalements)): ?>
            <p class="text-muted mb-0">Aucun signalement</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Localisation</th>
                            <th>Type</th>
                            <th>Problème</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($signalements as $sig): ?>
                            <tr>
                                <td><?= e($sig->user_nom) ?></td>
                                <td><?= e($sig->quartier_nom) ?>, <?= e($sig->ville_nom) ?></td>
                                <td><?= service_icon($sig->type_service) ?></td>
                                <td class="small"><?= e(truncate($sig->description, 50)) ?></td>
                                <td><?= status_badge($sig->statut) ?></td>
                                <td class="small text-muted"><?= time_ago($sig->created_at) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Coupures actives -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-lightning-charge me-2"></i>
            Coupures actives/planifiées
        </h5>
    </div>
        <div class="card-body">
        <?php if (empty($coupures)): ?>
            <p class="text-muted mb-0">Aucune coupure active</p>
        <?php else: ?>
            <?php
                // Inverse l'ordre du tableau $coupures pour que le plus récent soit en premier.
                $coupures = array_reverse($coupures, true);
            ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Liste quartier</th>
                            <th>Service</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Motif</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupures as $coupure): ?>
                            <tr>
                                <td><?= e($coupure->quartiers_list) ?></td>
                                <td><?= service_icon($coupure->type_service) ?></td>
                                <td class="small"><?= format_datetime($coupure->date_debut) ?></td>
                                <td class="small"><?= format_datetime($coupure->date_fin) ?></td>
                                <td><?= e($coupure->motif) ?></td>
                                <td><?= status_badge($coupure->statut) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>