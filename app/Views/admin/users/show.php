<?php $title = 'Détail Utilisateur #' . $user->id;

<div class="container py-4">
  <div class="row">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i> Détail utilisateur</h5>
          <div>
            <a href="<?= site_url('admin/users') ?>" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left"></i> Retour</a>
            <a href="<?= site_url('admin/users/edit/'.$user['id']) ?>" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen me-1"></i> Éditer</a>
          </div>
        </div>

        <div class="card-body">
          <?php if (!isset($user)): ?>
            <div class="alert alert-warning">Utilisateur introuvable.</div>
          <?php else: ?>
            <dl class="row">
              <dt class="col-sm-4">Nom complet</dt>
              <dd class="col-sm-8"><?= esc($user['nom'].' '.$user['prenom']) ?></dd>

              <dt class="col-sm-4">Email</dt>
              <dd class="col-sm-8"><?= esc($user['email']) ?></dd>

              <dt class="col-sm-4">Rôle</dt>
              <dd class="col-sm-8">
                <span class="badge <?= $user['role'] === 'admin' ? 'bg-primary' : 'bg-secondary' ?>"><?= esc($user['role']) ?></span>
              </dd>

              <dt class="col-sm-4">Ville / Quartier</dt>
              <dd class="col-sm-8">
                <?php if (!empty($user['ville_nom'])): ?>
                  <div><?= esc($user['ville_nom']) ?> <small class="text-muted">(ville)</small></div>
                <?php endif; ?>
                <?php if (!empty($user['quartier_nom'])): ?>
                  <div><?= esc($user['quartier_nom']) ?> <small class="text-muted">(quartier)</small></div>
                <?php endif; ?>
                <?php if (empty($user['ville_nom']) && empty($user['quartier_nom'])): ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </dd>

              <dt class="col-sm-4">Statut</dt>
              <dd class="col-sm-8">
                <?php if ($user['is_active']): ?>
                  <span class="badge bg-success">Actif</span>
                <?php else: ?>
                  <span class="badge bg-danger">Désactivé</span>
                <?php endif; ?>
              </dd>

              <dt class="col-sm-4">Créé le</dt>
              <dd class="col-sm-8"><?= isset($user['created_at']) ? date('Y-m-d H:i', strtotime($user['created_at'])) : '-' ?></dd>
              <dt class="col-sm-4">Dernière mise à jour</dt>
              <dd class="col-sm-8"><?= isset($user['updated_at']) ? date('Y-m-d H:i', strtotime($user['updated_at'])) : '-' ?></dd>
            </dl>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <!-- Petite carte d'action -->
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h6 class="card-title">Actions rapides</h6>
          <a href="<?= site_url('admin/users/reset_password/'.$user['id']) ?>" class="btn btn-sm btn-outline-secondary w-100 mb-2">
            <i class="fa-solid fa-key me-1"></i> Réinitialiser mot de passe
          </a>

          <form action="<?= site_url('admin/users/toggle_active/'.$user['id']) ?>" method="post" onsubmit="return confirm('Êtes-vous sûr ?');">
            <?= csrf_field() ?>
            <button class="btn btn-sm <?= $user['is_active'] ? 'btn-danger' : 'btn-success' ?> w-100 mb-2">
              <i class="fa-solid <?= $user['is_active'] ? 'fa-user-slash' : 'fa-user-check' ?> me-1"></i>
              <?= $user['is_active'] ? 'Désactiver' : 'Activer' ?>
            </button>
          </form>

          <form action="<?= site_url('admin/users/delete/'.$user['id']) ?>" method="post" onsubmit="return confirm('Supprimer cet utilisateur ?');">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-danger w-100"><i class="fa-solid fa-trash me-1"></i> Supprimer</button>
          </form>
        </div>
      </div>

      <!-- Info ville quick -->
      <?php if(!empty($user['ville_nom'])): ?>
        <div class="card shadow-sm">
          <div class="card-body">
            <h6 class="card-title">Ville</h6>
            <p class="mb-0"><?= esc($user['ville_nom']) ?> <small class="text-muted d-block">code: <?= esc($user['ville_code'] ?? '-') ?></small></p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
