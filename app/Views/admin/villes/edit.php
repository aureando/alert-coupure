<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fa-solid fa-city me-2"></i> Éditer Ville</h5>
      <div>
        <a href="<?= site_url('admin/villes') ?>" class="btn btn-sm btn-outline-secondary">
          <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
      </div>
    </div>

    <div class="card-body">
      <!-- Attendre la variable $ville (associative) -->
      <?php if (!isset($ville)): ?>
        <div class="alert alert-warning">Ville introuvable.</div>
      <?php else: ?>
        <form action="<?= site_url('admin/villes/update/'.$ville['id']) ?>" method="post" class="row g-3">
          <?= csrf_field() ?>

          <div class="col-md-6">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" value="<?= esc($ville['nom']) ?>" class="form-control" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Code</label>
            <input type="text" name="code" value="<?= esc($ville['code'] ?? '') ?>" class="form-control" required>
          </div>

          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" type="submit">
              <i class="fa-solid fa-save me-1"></i> Enregistrer
            </button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
