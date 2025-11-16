<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fa-solid fa-bolt me-2"></i> Éditer Coupure</h5>
      <div>
        <a href="<?= site_url('admin/coupures') ?>" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left"></i> Retour</a>
      </div>
    </div>

    <div class="card-body">
      <?php if (!isset($coupure)): ?>
        <div class="alert alert-warning">Coupure introuvable.</div>
      <?php else: ?>
        <form action="<?= site_url('admin/coupures/update/'.$coupure['id']) ?>" method="post" class="row g-3">
          <?= csrf_field() ?>

          <div class="col-md-6">
            <label class="form-label">Quartier</label>
            <select name="quartier_id" class="form-select" required>
              <?php foreach ($quartiers as $q): ?>
                <option value="<?= $q['id'] ?>" <?= $q['id'] == $coupure['quartier_id'] ? 'selected' : '' ?>>
                  <?= esc($q['nom']) ?> — <?= esc($villes[$q['ville_id']]['nom'] ?? '') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Date début</label>
            <input type="datetime-local" name="date_debut" class="form-control" value="<?= isset($coupure['date_debut']) ? date('Y-m-d\TH:i', strtotime($coupure['date_debut'])) : '' ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Date fin</label>
            <input type="datetime-local" name="date_fin" class="form-control" value="<?= isset($coupure['date_fin']) ? date('Y-m-d\TH:i', strtotime($coupure['date_fin'])) : '' ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control"><?= esc($coupure['description'] ?? '') ?></textarea>
          </div>

          <div class="col-12 text-end">
            <button class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Enregistrer</button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
