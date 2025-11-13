<?php $title = 'Accueil'; ?>

<!-- Hero Section -->
<div class="hero-section mb-5">
    <div class="row align-items-center">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="bi bi-lightning-charge-fill me-2"></i>
                Alert Coupure
            </h1>
            <p class="lead mb-4">
                Restez informés des coupures d'eau et d'électricité à Madagascar en temps réel
            </p>
            
            <?php if (!is_logged_in()): ?>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="<?= url('/register') ?>" class="btn btn-light btn-lg px-4">
                        <i class="bi bi-person-plus me-2"></i>
                        Créer un compte
                    </a>
                    <a href="<?= url('/login') ?>" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Se connecter
                    </a>
                </div>
            <?php else: ?>
                <a href="<?= url(is_admin() ? '/admin' : '/dashboard') ?>" class="btn btn-light btn-lg px-4">
                    <i class="bi bi-grid-3x3-gap me-2"></i>
                    Accéder au tableau de bord
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Barre de recherche -->
<div class="search-bar mb-5">
    <h4 class="mb-3 fw-semibold">
        <i class="bi bi-search me-2 text-primary"></i>
        Rechercher des coupures
    </h4>
    
    <form method="GET" action="<?= url('/') ?>" class="row g-3" id="searchForm">
        <div class="col-md-3">
            <label for="ville" class="form-label">Ville</label>
            <select class="form-select" id="ville" name="ville">
                <option value="">-- Toutes les villes --</option>
                <?php foreach ($villes as $ville): ?>
                    <option value="<?= $ville->id ?>" <?= (isset($villeId) && $villeId == $ville->id) ? 'selected' : '' ?>>
                        <?= e($ville->nom) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="col-md-3">
            <label for="quartier" class="form-label">Quartier</label>
            <select class="form-select" id="quartier" name="quartier" <?= empty($quartiers) ? 'disabled' : '' ?>>
                <option value="">-- Tous les quartiers --</option>
                <?php foreach ($quartiers as $quartier): ?>
                    <option value="<?= $quartier->id ?>" <?= (isset($quartierId) && $quartierId == $quartier->id) ? 'selected' : '' ?>>
                        <?= e($quartier->nom) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="col-md-3">
            <label for="type_service" class="form-label">Type de service</label>
            <select class="form-select" id="type_service" name="type_service">
                <option value="">-- Tous --</option>
                <option value="electricite" <?= (isset($typeService) && $typeService === 'electricite') ? 'selected' : '' ?>>Électricité</option>
                <option value="eau" <?= (isset($typeService) && $typeService === 'eau') ? 'selected' : '' ?>>Eau</option>
                <option value="les_deux" <?= (isset($typeService) && $typeService === 'les_deux') ? 'selected' : '' ?>>Les deux</option>
            </select>
        </div>
        
        <div class="col-md-3">
            <label class="form-label d-block">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search me-2"></i>
                Rechercher
            </button>
        </div>
    </form>
</div>

<!-- Script pour charger quartiers dynamiquement -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const villeSelect = document.getElementById('ville');
    const quartierSelect = document.getElementById('quartier');
    
    villeSelect.addEventListener('change', function() {
        const villeId = this.value;
        
        if (!villeId) {
            quartierSelect.innerHTML = '<option value="">-- Tous les quartiers --</option>';
            quartierSelect.disabled = true;
            return;
        }
        
        quartierSelect.innerHTML = '<option value="">Chargement...</option>';
        quartierSelect.disabled = true;
        
        fetch('<?= BASE_URL ?>/api/quartiers/' + villeId)
            .then(response => response.json())
            .then(data => {
                quartierSelect.innerHTML = '<option value="">-- Tous les quartiers --</option>';
                
                if (data && data.length > 0) {
                    data.forEach(quartier => {
                        const option = document.createElement('option');
                        option.value = quartier.id;
                        option.textContent = quartier.nom;
                        quartierSelect.appendChild(option);
                    });
                    quartierSelect.disabled = false;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                quartierSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            });
    });
});
</script>
</div>

<!-- Section des coupures récentes -->
<div class="row">
    <div class="col-12">
        <h4 class="mb-4 fw-semibold">
            <i class="bi bi-clock-history me-2 text-primary"></i>
            Coupures récentes
        </h4>
    </div>
</div>

<?php if (empty($coupures)): ?>
    
    <!-- Aucune coupure -->
    <div class="alert alert-success border-0 shadow-sm">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-3 me-3"></i>
            <div>
                <h5 class="alert-heading mb-1">Aucune coupure planifiée !</h5>
                <p class="mb-0 small">Il n'y a actuellement aucune coupure prévue dans les prochains jours.</p>
            </div>
        </div>
    </div>
    
<?php else: ?>
    
    <!-- Liste des coupures -->
    <div class="row g-4">
        <?php foreach ($coupures as $coupure): ?>
            
            <div class="col-md-6 col-lg-4">
                <div class="card coupure-card h-100 <?= $coupure->type_service ?> fade-in">
                    <div class="card-body">
                        
                        <!-- En-tête -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1 fw-bold">
                                    <?= e($coupure->quartier_nom) ?>  <!-- LE QUARTIER en grand -->
                                </h5>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <?= e($coupure->ville_nom) ?>  <!-- La ville en petit -->
                                </p>
                            </div>
                            <div>
                                <?= service_icon($coupure->type_service) ?>
                            </div>
                        </div>  
                        
                        <!-- Dates -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center text-muted small mb-1">
                                <i class="bi bi-calendar-event me-2"></i>
                                <strong class="me-1">Début :</strong>
                                <?= format_datetime($coupure->date_debut) ?>
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-calendar-check me-2"></i>
                                <strong class="me-1">Fin :</strong>
                                <?= format_datetime($coupure->date_fin) ?>
                            </div>
                        </div>
                        
                        <!-- Motif -->
                        <?php if ($coupure->motif): ?>
                            <div class="mb-3">
                                <span class="badge bg-secondary">
                                    <?= e($coupure->motif) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Description -->
                        <?php if ($coupure->description): ?>
                            <p class="card-text small text-muted mb-3">
                                <?= e(truncate($coupure->description, 100)) ?>
                            </p>
                        <?php endif; ?>
                        
                        <!-- Statut -->
                        <div class="d-flex justify-content-between align-items-center">
                            <?= status_badge($coupure->statut) ?>
                            <small class="text-muted">
                                <?= time_ago($coupure->created_at) ?>
                            </small>
                        </div>
                        
                    </div>
                </div>
            </div>
            
        <?php endforeach; ?>
    </div>
    
<?php endif; ?>

<!-- Call to action -->
<?php if (!is_logged_in()): ?>
    <div class="mt-5 text-center">
        <div class="card border-primary shadow-sm">
            <div class="card-body py-4">
                <h5 class="card-title mb-3">
                    <i class="bi bi-bell me-2 text-primary"></i>
                    Recevez des notifications personnalisées
                </h5>
                <p class="text-muted mb-4">
                    Créez un compte pour être alerté des coupures dans votre quartier et signaler des pannes
                </p>
                <a href="<?= url('/register') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-person-plus me-2"></i>
                    S'inscrire gratuitement
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>