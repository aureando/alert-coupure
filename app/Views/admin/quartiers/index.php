<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fa-solid fa-location-dot me-2"></i> Quartiers</h4>
    <div>
      <a href="<?= site_url('admin/quartiers/create') ?>" class="btn btn-success">
        <i class="fa-solid fa-plus me-1"></i> Nouveau Quartier
      </a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Nom</th>
              <th>Ville</th>
              <th>Créé le</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($quartiers) && is_array($quartiers)): ?>
              <?php foreach ($quartiers as $q): ?>
                <tr>
                  <td><?= $q['id'] ?></td>
                  <td><?= esc($q['nom']) ?></td>
                  <td>
                    <?php if (isset($q['ville_nom'])): ?>
                      <span class="badge bg-info text-dark"><?= esc($q['ville_nom']) ?></span>
                    <?php elseif (isset($villes) && !empty($villes[$q['ville_id']])): ?>
                      <span class="badge bg-info text-dark"><?= esc($villes[$q['ville_id']]['nom']) ?></span>
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>
                  <td><?= isset($q['created_at']) ? date('Y-m-d H:i', strtotime($q['created_at'])) : '-' ?></td>
                  <td class="text-end">
                    <a href="<?= site_url('admin/quartiers/show/'.$q['id']) ?>" class="btn btn-sm btn-outline-primary" title="Voir">
                      <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="<?= site_url('admin/quartiers/edit/'.$q['id']) ?>" class="btn btn-sm btn-outline-warning" title="Éditer">
                      <i class="fa-solid fa-pen"></i>
                    </a>
                    <form action="<?= site_url('admin/quartiers/delete/'.$q['id']) ?>" method="post" class="d-inline-block" onsubmit="return confirm('Supprimer ce quartier ?');">
                      <?= csrf_field() ?>
                      <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center py-3">Aucun quartier trouvé.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
