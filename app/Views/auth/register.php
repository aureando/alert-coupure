<?php $title = 'Inscription'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-8 col-lg-6">
        
        <!-- Carte d'inscription -->
        <div class="card shadow-lg border-0 fade-in">
            <div class="card-body p-5">
                
                <!-- Titre -->
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-2">Créer un compte</h3>
                    <p class="text-muted small">Rejoignez Alert Coupure dès maintenant</p>
                </div>
                
                <!-- Formulaire -->
                <form method="POST" action="<?= url('/register') ?>" id="registerForm">
                    
                    <div class="row">
                        
                        <!-- Nom -->
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">
                                <i class="bi bi-person me-1"></i> Nom <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="nom" 
                                name="nom" 
                                placeholder="Rakoto"
                                required
                            >
                        </div>
                        
                        <!-- Prénom -->
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">
                                <i class="bi bi-person me-1"></i> Prénom <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="prenom" 
                                name="prenom" 
                                placeholder="Jean"
                                required
                            >
                        </div>
                        
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1"></i> Adresse email <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            placeholder="jean.rakoto@gmail.com"
                            required
                        >
                    </div>
                    
                    <div class="row">
                        
                        <!-- Ville -->
                        <div class="col-md-6 mb-3">
                            <label for="ville_id" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i> Ville <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select" 
                                id="ville_id" 
                                name="ville_id" 
                                required
                            >
                                <option value="">-- Choisir une ville --</option>
                                <?php foreach ($villes as $ville): ?>
                                    <option value="<?= $ville->id ?>">
                                        <?= e($ville->nom) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Quartier -->
                        <div class="col-md-6 mb-3">
                            <label for="quartier_id" class="form-label">
                                <i class="bi bi-pin-map me-1"></i> Quartier <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select" 
                                id="quartier_id" 
                                name="quartier_id" 
                                required
                                disabled
                            >
                                <option value="">-- Choisir d'abord une ville --</option>
                            </select>
                        </div>
                        
                    </div>
                    
                    <div class="row">
                        
                        <!-- Mot de passe -->
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock me-1"></i> Mot de passe <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="••••••••"
                                required
                            >
                            <small class="text-muted">Min. 8 caractères</small>
                        </div>
                        
                        <!-- Confirmation -->
                        <div class="col-md-6 mb-4">
                            <label for="password_confirm" class="form-label">
                                <i class="bi bi-lock-fill me-1"></i> Confirmation <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password_confirm" 
                                name="password_confirm" 
                                placeholder="••••••••"
                                required
                            >
                        </div>
                        
                    </div>
                    
                    <!-- Bouton d'inscription -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-person-plus me-2"></i>
                            S'inscrire
                        </button>
                    </div>
                    
                </form>
                
                <!-- Lien connexion -->
                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">
                        Vous avez déjà un compte ?
                        <a href="<?= url('/login') ?>" class="text-primary fw-semibold text-decoration-none">
                            Connectez-vous
                        </a>
                    </p>
                </div>
                
            </div>
        </div>
        
    </div>
</div>

<!-- Script pour charger les quartiers dynamiquement -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const villeSelect = document.getElementById('ville_id');
    const quartierSelect = document.getElementById('quartier_id');
    
    villeSelect.addEventListener('change', function() {
        const villeId = this.value;
        
        if (!villeId) {
            quartierSelect.innerHTML = '<option value="">-- Choisir d'abord une ville --</option>';
            quartierSelect.disabled = true;
            return;
        }
        
        // Charger les quartiers via AJAX
        fetch(`<?= url('/api/quartiers/') ?>${villeId}`)
            .then(response => response.json())
            .then(data => {
                quartierSelect.innerHTML = '<option value="">-- Choisir un quartier --</option>';
                
                data.forEach(quartier => {
                    const option = document.createElement('option');
                    option.value = quartier.id;
                    option.textContent = quartier.nom;
                    quartierSelect.appendChild(option);
                });
                
                quartierSelect.disabled = false;
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement des quartiers');
            });
    });
});
</script>