<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fa-solid fa-pen me-2"></i> Éditer Quartier</h5>
      <a href="<?= site_url('admin/quartiers') ?>" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card-body">
      <?php if (!isset($quartier)): ?>
        <div class="alert alert-warning">Quartier introuvable.</div>
      <?php else: ?>
        <form action="<?= site_url('admin/quartiers/update/'.$quartier['id']) ?>" method="post" class="row g-3">
          <?= csrf_field() ?>

          <div class="col-md-6">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" value="<?= esc($quartier['nom']) ?>" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Ville</label>
            <select name="ville_id" class="form-select" required>
              <?php foreach ($villes as $v): ?>
                <option value="<?= $v['id'] ?>" <?= $v['id'] == $quartier['ville_id'] ? 'selected' : '' ?>>
                  <?= esc($v['nom']) ?> (<?= esc($v['code'] ?? '') ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12 text-end">
            <button class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Mettre à jour</button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
