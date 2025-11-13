<?php $title = 'Gestion des Signalements'; ?>

<div class="mb-4">
    <h2>
        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
        Gestion des Signalements
    </h2>
</div>

<?php if (empty($signalements)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Aucun signalement.
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Localisation</th>
                            <th>Service</th>
                            <th>Problème</th>
                            <th>Description</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($signalements as $sig): ?>
                            <tr>
                                <td><?= $sig->id ?></td>
                                <td class="small">
                                    <strong><?= e($sig->user_nom) ?></strong><br>
                                    <small class="text-muted"><?= e($sig->user_email) ?></small>
                                </td>
                                <td class="small">
                                    <?= e($sig->quartier_nom) ?><br>
                                    <small class="text-muted"><?= e($sig->ville_nom) ?></small>
                                </td>
                                <td><?= service_icon($sig->type_service) ?></td>
                                <td class="small"><?= e(str_replace('_', ' ', $sig->type_probleme)) ?></td>
                                <td class="small"><?= e(truncate($sig->description, 40)) ?></td>
                                <td><?= status_badge($sig->statut) ?></td>
                                <td class="small text-muted"><?= time_ago($sig->created_at) ?></td>
                                <td>
                                    <a href="<?= url('/admin/signalements/' . $sig->id) ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>