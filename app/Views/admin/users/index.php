<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fa-solid fa-users me-2"></i> Utilisateurs</h4>
    <a href="<?= site_url('admin/users/create') ?>" class="btn btn-success">
      <i class="fa-solid fa-user-plus me-1"></i> Nouvel utilisateur
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Rôle</th>
              <th>Localisation</th>
              <th>Statut</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($users)): foreach ($users as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><?= esc($u['nom'].' '.$u['prenom']) ?></td>
                <td><?= esc($u['email']) ?></td>
                <td>
                  <?php if ($u['role'] === 'admin'): ?>
                    <span class="badge bg-primary">admin</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">user</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($u['ville_nom'])): ?>
                    <small class="text-muted"><?= esc($u['ville_nom']) ?></small>
                  <?php elseif (!empty($u['quartier_nom'])): ?>
                    <small class="text-muted"><?= esc($u['quartier_nom']) ?></small>
                  <?php else: ?>
                    <small class="text-muted">—</small>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($u['is_active']): ?>
                    <span class="badge bg-success">Actif</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Désactivé</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <a href="<?= site_url('admin/users/show/'.$u['id']) ?>" class="btn btn-sm btn-outline-primary" title="Voir"><i class="fa-solid fa-eye"></i></a>
                  <a href="<?= site_url('admin/users/edit/'.$u['id']) ?>" class="btn btn-sm btn-outline-warning" title="Éditer"><i class="fa-solid fa-pen"></i></a>
                  <form action="<?= site_url('admin/users/delete/'.$u['id']) ?>" method="post" class="d-inline-block" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="7" class="text-center py-3">Aucun utilisateur.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
