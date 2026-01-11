<?php
/**
 * Page de connexion - Portail Campus
 * Université du Sénégal - Licence 3 GLAR
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    $redirect = $_SESSION['redirect_after_login'] ?? '/students/list.php';
    unset($_SESSION['redirect_after_login']);
    header('Location: ' . $redirect);
    exit;
}

$error = '';
$attempts = $_SESSION['login_attempts'] ?? 0;
$lastAttempt = $_SESSION['last_login_attempt'] ?? 0;

// Bloquer après 5 tentatives pendant 5 minutes
$isBlocked = ($attempts >= 5 && (time() - $lastAttempt) < 300);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isBlocked) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, password, nom, prenom FROM admins WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Réinitialiser les tentatives
            unset($_SESSION['login_attempts']);
            unset($_SESSION['last_login_attempt']);
            
            // Mettre à jour la date de dernière connexion
            $updateStmt = $pdo->prepare('UPDATE admins SET last_login = NOW() WHERE id = ?');
            $updateStmt->execute([$user['id']]);
            
            // Connecter l'utilisateur
            loginUser($user['id'], $user['username'], $user['nom'], $user['prenom']);
            
            setFlashMessage('success', '👋 Bienvenue, ' . $user['prenom'] . ' ! Vous êtes maintenant connecté.');
            
            // Redirection vers la page demandée ou par défaut
            $redirect = $_SESSION['redirect_after_login'] ?? '/students/list.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            // Incrémenter les tentatives
            $_SESSION['login_attempts'] = $attempts + 1;
            $_SESSION['last_login_attempt'] = time();
            $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
        }
    }
}

$pageTitle = 'Connexion - Portail Campus';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Connexion au Portail Campus - Gestion des étudiants">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="login-bg">
    <div class="container">
        <?php 
        // Afficher le message flash de session expirée
        $flash = getFlashMessage();
        if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'warning' ? 'warning' : 'info' ?> mb-4 text-center" style="max-width: 450px; margin: 0 auto 1rem;">
            <i class="bi bi-<?= $flash['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle' ?>"></i>
            <?= e($flash['message']) ?>
        </div>
        <?php endif; ?>
        
        <div class="card login-card shadow-lg">
            <div class="card-header login-header text-center text-white">
                <i class="bi bi-mortarboard-fill"></i>
                <h3>Portail Campus</h3>
                <p class="mb-0 opacity-75">Connexion Administration</p>
            </div>
            <div class="card-body p-4">
                <?php if ($isBlocked): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-shield-exclamation"></i>
                    <strong>Compte temporairement bloqué</strong><br>
                    Trop de tentatives de connexion. Réessayez dans <?= ceil((300 - (time() - $lastAttempt)) / 60) ?> minute(s).
                </div>
                <?php elseif ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <?= e($error) ?>
                    <?php if ($attempts > 0 && $attempts < 5): ?>
                    <br><small class="mt-1 d-block">Tentative <?= $attempts ?>/5</small>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="" <?= $isBlocked ? 'style="opacity: 0.5; pointer-events: none;"' : '' ?>>
                    <div class="mb-4">
                        <label for="username" class="form-label">
                            <i class="bi bi-person"></i> Nom d'utilisateur
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control form-control-lg" id="username" name="username" 
                                   value="<?= e($_POST['username'] ?? '') ?>" required autofocus
                                   placeholder="Entrez votre identifiant" autocomplete="username">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Mot de passe
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" required
                                   placeholder="Entrez votre mot de passe" autocomplete="current-password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" title="Afficher/Masquer">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" <?= $isBlocked ? 'disabled' : '' ?>>
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </button>
                </form>
                
                <hr>
                <div class="text-center">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Identifiants par défaut<br>
                        <code>admin</code> / <code>admin123</code>
                    </small>
                </div>
            </div>
        </div>
        <p class="text-center text-white mt-4 opacity-75">
            <i class="bi bi-shield-lock"></i> Connexion sécurisée - Session de 30 min
        </p>
        <p class="text-center text-white-50 small">
            &copy; <?= date('Y') ?> Portail Campus - Licence 3 GLAR
        </p>
    </div>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>
