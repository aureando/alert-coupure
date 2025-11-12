<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Administration' ?> - Alert Coupure</title>
    
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">
    
    <!-- Navbar Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= url('/admin') ?>">
                <i class="bi bi-speedometer2 me-2"></i>
                <strong>Admin</strong> Alert Coupure
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= is_active('/admin/villes') ? 'active' : '' ?>" href="<?= url('/admin/villes') ?>">
                            <i class="bi bi-building me-1"></i> Villes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= is_active('/admin/quartiers') ? 'active' : '' ?>" href="<?= url('/admin/quartiers') ?>">
                            <i class="bi bi-map me-1"></i> Quartiers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= is_active('/admin/coupures') ? 'active' : '' ?>" href="<?= url('/admin/coupures') ?>">
                            <i class="bi bi-lightning-charge me-1"></i> Coupures
                        </a>
                    </li>
                    
                    <!-- Notifications signalements -->
                    <?php 
                    $signalementModel = new App\Models\Signalement();
                    $nbSignalementsAttente = $signalementModel->countByStatus('signale');
                    ?>
                    <li class="nav-item position-relative">
                        <a class="nav-link <?= is_active('/admin/signalements') ? 'active' : '' ?>" href="<?= url('/admin/signalements') ?>">
                            <i class="bi bi-exclamation-triangle me-1"></i> Signalements
                            <?php if ($nbSignalementsAttente > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-1"><?= $nbSignalementsAttente ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= is_active('/admin/users') ? 'active' : '' ?>" href="<?= url('/admin/users') ?>">
                            <i class="bi bi-people me-1"></i> Utilisateurs
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/') ?>">
                            <i class="bi bi-house-door me-1"></i> Site public
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= e(auth_user()['prenom']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('/logout') ?>">
                                <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Contenu -->
    <main class="flex-grow-1 py-4">
        <div class="container-fluid">
            <?php display_all_flash(); ?>
            <?= $content ?>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white-50 py-3 text-center small">
        &copy; <?= date('Y') ?> Alert Coupure - Administration
    </footer>
    
    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>