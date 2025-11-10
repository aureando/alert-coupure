<?php $title = 'Connexion'; ?>

<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5 col-lg-4">
        
        <!-- Carte de connexion -->
        <div class="card shadow-lg border-0 fade-in">
            <div class="card-body p-5">
                
                <!-- Logo et titre -->
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="bi bi-lightning-charge-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-2">Connexion</h3>
                    <p class="text-muted small">Accédez à votre compte Alert Coupure</p>
                </div>
                
                <!-- Formulaire -->
                <form method="POST" action="<?= url('/login') ?>">
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1"></i> Adresse email
                        </label>
                        <input 
                            type="email" 
                            class="form-control form-control-lg" 
                            id="email" 
                            name="email" 
                            placeholder="votre@email.com"
                            required
                            autofocus
                        >
                    </div>
                    
                    <!-- Mot de passe -->
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock me-1"></i> Mot de passe
                        </label>
                        <input 
                            type="password" 
                            class="form-control form-control-lg" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    
                    <!-- Bouton de connexion -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Se connecter
                        </button>
                    </div>
                    
                </form>
                
                <!-- Lien inscription -->
                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">
                        Pas encore de compte ?
                        <a href="<?= url('/register') ?>" class="text-primary fw-semibold text-decoration-none">
                            Inscrivez-vous
                        </a>
                    </p>
                </div>
                
            </div>
        </div>
        
        <!-- Info admin -->
        <div class="text-center mt-3">
            <div class="alert alert-info border-0 shadow-sm">
                <small>
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Admin :</strong> admin@alertcoupure.mg / Admin@2024
                </small>
            </div>
        </div>
        
    </div>
</div>