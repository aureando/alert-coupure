<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Alert Coupure' ?> - Gestion des coupures à Madagascar</title>
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= asset('css/bootstrap-icons.css') ?>">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= url('/') ?>">
                <i class="bi bi-lightning-charge-fill me-2 fs-4"></i>
                <span class="fw-bold">Alert Coupure</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= is_active('/') ? 'active' : '' ?>" href="<?= url('/') ?>">
                            <i class="bi bi-house-door me-1"></i> Accueil
                        </a>
                    </li>
                    
                    <?php if (is_logged_in()): ?>
                        
                        <?php if (is_admin()): ?>
                            <!-- Menu Admin -->
                            <li class="nav-item">
                                <a class="nav-link <?= is_active('/admin') ? 'active' : '' ?>" href="<?= url('/admin') ?>">
                                    <i class="bi bi-speedometer2 me-1"></i> Administration
                                </a>
                            </li>
                        <?php else: ?>
                            <!-- Menu Utilisateur -->
                            <li class="nav-item">
                                <a class="nav-link <?= is_active('/dashboard') ? 'active' : '' ?>" href="<?= url('/dashboard') ?>">
                                    <i class="bi bi-grid-3x3-gap me-1"></i> Tableau de bord
                                </a>
                            </li>
                            <li class="nav-item position-relative">
                                <a class="nav-link <?= is_active('/signalements') ? 'active' : '' ?>" href="<?= url('/signalements') ?>">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Mes signalements
                                </a>
                            </li>
                            
                            <!-- Notification coupures actives -->
                            <?php 
                            $user = auth_user();
                            if ($user && isset($user['quartier_id'])) {
                                $coupureModel = new App\Models\Coupure();
                                $coupuresActives = $coupureModel->getActiveByQuartier($user['quartier_id']);
                                $nbCoupures = count($coupuresActives);
                            } else {
                                $nbCoupures = 0;
                            }
                            ?>
                            <?php if ($nbCoupures > 0): ?>
                                <li class="nav-item">
                                    <a class="nav-link text-warning fw-bold" href="<?= url('/dashboard') ?>#coupures">
                                        <i class="bi bi-bell-fill me-1"></i>
                                        <span class="badge bg-warning text-dark"><?= $nbCoupures ?></span>
                                        Coupure<?= $nbCoupures > 1 ? 's' : '' ?> active<?= $nbCoupures > 1 ? 's' : '' ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Dropdown utilisateur -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= e(auth_user()['prenom'] ?? 'Utilisateur') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= url('/logout') ?>">
                                    <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                                </a></li>
                            </ul>
                        </li>
                        
                    <?php else: ?>
                        <!-- Menu visiteur -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/login') ?>">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Connexion
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light btn-sm ms-2" href="<?= url('/register') ?>">
                                <i class="bi bi-person-plus me-1"></i> Inscription
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Contenu principal -->
    <main class="flex-grow-1 py-4">
        <div class="container">
            <!-- Messages flash -->
            <?php display_all_flash(); ?>
            
            <!-- Contenu de la page -->
            <?= $content ?>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                        Alert Coupure
                    </h5>
                    <p class="text-white-50 small">
                        Plateforme de gestion des coupures d'eau et d'électricité à Madagascar.
                        Restez informés en temps réel.
                    </p>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Liens rapides</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('/') ?>" class="text-white-50 text-decoration-none small">Accueil</a></li>
                        <?php if (!is_logged_in()): ?>
                            <li><a href="<?= url('/login') ?>" class="text-white-50 text-decoration-none small">Connexion</a></li>
                            <li><a href="<?= url('/register') ?>" class="text-white-50 text-decoration-none small">Inscription</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Contact</h6>
                    <p class="text-white-50 small mb-1">
                        <i class="bi bi-envelope me-2"></i>
                        contact@alertcoupure.mg
                    </p>
                    <p class="text-white-50 small">
                        <i class="bi bi-telephone me-2"></i>
                        +261 34 XX XXX XX
                    </p>
                </div>
            </div>
            <hr class="border-secondary my-3">
            <div class="text-center text-white-50 small">
                &copy; <?= date('Y') ?> Alert Coupure. Tous droits réservés.
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
    
    <!-- Custom JS -->
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>