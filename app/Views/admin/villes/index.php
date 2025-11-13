<?php $title = 'Gestion des Villes'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-building text-primary me-2"></i>
        Gestion des Villes
    </h2>
    <a href="<?= url('/admin/villes/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        Nouvelle Ville
    </a>
</div>

<?php if (empty($villes)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Aucune ville enregistrée.
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Code</th>
                            <th>Nombre de Quartiers</th>
                            <th>Date de création</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($villes as $ville): ?>
                            <tr>
                                <td><?= $ville->id ?></td>
                                <td>
                                    <strong><?= e($ville->nom) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= e($ville->code) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= $ville->nb_quartiers ?? 0 ?> quartier(s)</span>
                                </td>
                                <td class="small text-muted">
                                    <?= format_date($ville->created_at) ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= url('/admin/villes/' . $ville->id . '/edit') ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= url('/admin/villes/' . $ville->id . '/delete') ?>" 
                                          class="d-inline"
                                          onsubmit="return confirm('Supprimer cette ville ? Tous les quartiers associés seront aussi supprimés !')">
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