<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portail Campus - Système de Gestion des Étudiants">
    <meta name="author" content="Licence 3 GLAR - Université du Sénégal">
    <title><?= e($pageTitle ?? 'Portail Campus') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <!-- Session Info Banner -->
    <div class="session-banner d-none d-md-flex">
        <i class="bi bi-shield-check"></i>
        <span>Connecté depuis <?= getSessionDuration() ?></span>
        <span class="mx-2">|</span>
        <i class="bi bi-clock"></i>
        <span>Session expire dans <?= floor(getTimeUntilTimeout() / 60) ?> min</span>
    </div>
    <?php endif; ?>

    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/index.php">
                <i class="bi bi-mortarboard-fill"></i> Portail Campus
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/students/list') !== false ? 'active' : '' ?>" href="/students/list.php">
                            <i class="bi bi-people"></i> Étudiants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/students/create') !== false ? 'active' : '' ?>" href="/students/create.php">
                            <i class="bi bi-person-plus"></i> Ajouter
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="avatar d-inline-flex me-2" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                <?= strtoupper(substr($_SESSION['user_prenom'], 0, 1) . substr($_SESSION['user_nom'], 0, 1)) ?>
                            </div>
                            <?= e(getCurrentUserName()) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header">
                                <strong><?= e(getUserLogin()) ?></strong><br>
                                <small class="text-muted">Connecté le <?= getLoginTime() ?></small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="/index.php">
                                    <i class="bi bi-house me-2"></i> Tableau de bord
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/students/list.php">
                                    <i class="bi bi-people me-2"></i> Gérer les étudiants
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/auth/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Connexion
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php
        $flash = getFlashMessage();
        if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'warning' ? 'warning' : 'info')) ?> alert-dismissible fade show flash-message" role="alert">
            <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-circle' : ($flash['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle')) ?>"></i>
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
