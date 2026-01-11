<?php
/**
 * Page d'accueil du Portail Campus
 */
$pageTitle = 'Accueil - Portail Campus';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/database.php';

// Statistiques si connecté
if (isLoggedIn()) {
    $totalStudents = $pdo->query('SELECT COUNT(*) FROM etudiants')->fetchColumn();
    $totalGLAR = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE filiere = 'GLAR'")->fetchColumn();
    $totalL3 = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE niveau = 'L3'")->fetchColumn();
}
?>

<div class="row">
    <div class="col-12 text-center mb-5">
        <div class="py-4">
            <i class="bi bi-mortarboard-fill display-1 text-primary mb-3" style="font-size: 4rem;"></i>
            <h1 class="display-4 fw-bold">
                Portail Campus
            </h1>
            <p class="lead text-muted fs-4">Système de Gestion des Étudiants</p>
        </div>
    </div>
</div>

<?php if (!isLoggedIn()): ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-shield-lock display-1 text-primary"></i>
                </div>
                <h4 class="fw-bold mb-3">Accès Administrateur</h4>
                <p class="text-muted mb-4">Connectez-vous pour accéder à la gestion des étudiants et aux fonctionnalités d'administration.</p>
                <a href="/auth/login.php" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-box-arrow-in-right"></i> Se connecter
                </a>
            </div>
        </div>
    </div>
</div>
<?php else: ?>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stat-card text-center">
            <i class="bi bi-people display-4 mb-2"></i>
            <h2><?= $totalStudents ?></h2>
            <p class="mb-0 opacity-75">Étudiants inscrits</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card text-center" style="background: linear-gradient(135deg, #00c853 0%, #69f0ae 100%);">
            <i class="bi bi-mortarboard display-4 mb-2"></i>
            <h2><?= $totalGLAR ?></h2>
            <p class="mb-0 opacity-75">Étudiants GLAR</p>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card text-center" style="background: linear-gradient(135deg, #ff9800 0%, #ffc107 100%);">
            <i class="bi bi-award display-4 mb-2"></i>
            <h2><?= $totalL3 ?></h2>
            <p class="mb-0 opacity-75">Étudiants L3</p>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card home-card h-100">
            <div class="card-body">
                <i class="bi bi-people fs-1 text-primary"></i>
                <h5>Liste des Étudiants</h5>
                <p class="text-muted">Consultez et gérez tous les étudiants inscrits dans le système</p>
                <a href="/students/list.php" class="btn btn-primary">
                    <i class="bi bi-list"></i> Voir la liste
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card home-card h-100">
            <div class="card-body">
                <i class="bi bi-person-plus fs-1 text-success"></i>
                <h5>Nouvel Étudiant</h5>
                <p class="text-muted">Ajouter un nouvel étudiant avec toutes ses informations</p>
                <a href="/students/create.php" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Ajouter
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card home-card h-100">
            <div class="card-body">
                <i class="bi bi-search fs-1 text-info"></i>
                <h5>Rechercher</h5>
                <p class="text-muted">Rechercher par nom, matricule, filière ou niveau</p>
                <a href="/students/list.php" class="btn btn-info text-white">
                    <i class="bi bi-search"></i> Rechercher
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row mt-5">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-bold"><i class="bi bi-info-circle text-primary"></i> À propos du projet</h5>
                <p class="mb-3">
                    Ce projet est une <strong>infrastructure complète</strong> pour un Portail Campus universitaire, 
                    déployée avec <strong>Docker</strong> et incluant les services suivants :
                </p>
                <div class="row">
                    <div class="col-md-2 col-4 text-center mb-3">
                        <i class="bi bi-hdd-network text-primary fs-3"></i>
                        <p class="small mb-0 mt-1">DNS</p>
                    </div>
                    <div class="col-md-2 col-4 text-center mb-3">
                        <i class="bi bi-router text-success fs-3"></i>
                        <p class="small mb-0 mt-1">DHCP</p>
                    </div>
                    <div class="col-md-2 col-4 text-center mb-3">
                        <i class="bi bi-globe text-info fs-3"></i>
                        <p class="small mb-0 mt-1">HTTP</p>
                    </div>
                    <div class="col-md-2 col-4 text-center mb-3">
                        <i class="bi bi-database text-warning fs-3"></i>
                        <p class="small mb-0 mt-1">MySQL</p>
                    </div>
                    <div class="col-md-2 col-4 text-center mb-3">
                        <i class="bi bi-folder-symlink text-danger fs-3"></i>
                        <p class="small mb-0 mt-1">Samba</p>
                    </div>
                    <div class="col-md-2 col-4 text-center mb-3">
                        <i class="bi bi-code-slash text-secondary fs-3"></i>
                        <p class="small mb-0 mt-1">PHP 8.2</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
