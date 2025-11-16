<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fa-solid fa-plus me-2"></i> Créer Quartier</h5>
      <a href="<?= site_url('admin/quartiers') ?>" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card-body">
      <form action="<?= site_url('admin/quartiers/store') ?>" method="post" class="row g-3">
        <?= csrf_field() ?>

        <div class="col-md-6">
          <label class="form-label">Nom du quartier</label>
          <input type="text" name="nom" class="form-control" required value="<?= set_value('nom') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Ville</label>
          <select name="ville_id" class="form-select" required>
            <option value="">— Choisir une ville —</option>
            <?php foreach ($villes as $v): ?>
              <option value="<?= $v['id'] ?>" <?= set_value('ville_id') == $v['id'] ? 'selected' : '' ?>>
                <?= esc($v['nom']) ?> (<?= esc($v['code'] ?? '') ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12 d-flex justify-content-end">
          <button class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
