<?php $title = 'Gestion des Coupures'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-lightning-charge text-warning me-2"></i>
        Gestion des Coupures
    </h2>
    <a href="<?= url('/admin/coupures/create') ?>" class="btn btn-warning text-dark">
        <i class="bi bi-plus-circle me-2"></i>
        Planifier une Coupure
    </a>
</div>

<?php if (empty($coupures)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Aucune coupure planifiée.
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Service</th>
                            <th>Quartiers</th>
                            <th>Dates</th>
                            <th>Motif</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coupures as $coupure): ?>
                            <tr>
                                <td><?= $coupure->id ?></td>
                                <td><?= service_icon($coupure->type_service) ?></td>
                                <td class="small">
                                    <?= e(truncate($coupure->quartiers_list ?? 'N/A', 50)) ?>
                                </td>
                                <td class="small">
                                    <div><strong>Début:</strong> <?= format_datetime($coupure->date_debut) ?></div>
                                    <div><strong>Fin:</strong> <?= format_datetime($coupure->date_fin) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= e($coupure->motif) ?></span>
                                </td>
                                <td><?= status_badge($coupure->statut) ?></td>
                                <td class="text-end">
                                    <a href="<?= url('/admin/coupures/' . $coupure->id . '/edit') ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= url('/admin/coupures/' . $coupure->id . '/delete') ?>" 
                                          class="d-inline"
                                          onsubmit="return confirm('Supprimer cette coupure ?')">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>