<?php
/**
 * Gestion des sessions utilisateur - Version améliorée
 * Portail Campus - Université du Sénégal
 */

// Configuration de la session sécurisée
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Durée d'expiration de session (30 minutes d'inactivité)
define('SESSION_TIMEOUT', 1800);

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier l'expiration de la session
checkSessionTimeout();

/**
 * Vérifie si la session a expiré
 */
function checkSessionTimeout(): void {
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            // Session expirée
            $wasLoggedIn = isLoggedIn();
            logoutUser();
            if ($wasLoggedIn) {
                $_SESSION['flash'] = [
                    'type' => 'warning',
                    'message' => 'Votre session a expiré. Veuillez vous reconnecter.'
                ];
            }
        }
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Redirige vers la page de login si non connecté
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /auth/login.php');
        exit;
    }
}

/**
 * Connecte un utilisateur
 */
function loginUser(int $id, string $username, string $nom, string $prenom): void {
    // Régénérer l'ID de session pour prévenir le fixation de session
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = $username;
    $_SESSION['user_nom'] = $nom;
    $_SESSION['user_prenom'] = $prenom;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}

/**
 * Déconnecte l'utilisateur
 */
function logoutUser(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Obtient le nom complet de l'utilisateur connecté
 */
function getCurrentUserName(): string {
    if (isLoggedIn()) {
        return $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
    }
    return 'Invité';
}

/**
 * Obtient l'ID de l'utilisateur connecté
 */
function getCurrentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtient le login/identifiant de l'utilisateur
 */
function getUserLogin(): ?string {
    return $_SESSION['username'] ?? null;
}

/**
 * Obtient la durée de connexion formatée
 */
function getSessionDuration(): string {
    if (!isset($_SESSION['login_time'])) {
        return '0 min';
    }
    $duration = time() - $_SESSION['login_time'];
    $hours = floor($duration / 3600);
    $minutes = floor(($duration % 3600) / 60);
    
    if ($hours > 0) {
        return "{$hours}h {$minutes}min";
    }
    return "{$minutes} min";
}

/**
 * Obtient le temps restant avant expiration
 */
function getTimeUntilTimeout(): int {
    if (!isset($_SESSION['last_activity'])) {
        return SESSION_TIMEOUT;
    }
    return max(0, SESSION_TIMEOUT - (time() - $_SESSION['last_activity']));
}

/**
 * Obtient la date/heure de connexion formatée
 */
function getLoginTime(): string {
    if (!isset($_SESSION['login_time'])) {
        return '-';
    }
    return date('d/m/Y à H:i', $_SESSION['login_time']);
}

/**
 * Vérifie la cohérence de la session (sécurité)
 */
function validateSession(): bool {
    // Vérifier l'IP (optionnel, peut poser problème avec les proxies)
    // if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    //     return false;
    // }
    
    // Vérifier le user agent
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        return false;
    }
    
    return true;
}

/**
 * Renouvelle la session
 */
function refreshSession(): void {
    $_SESSION['last_activity'] = time();
}
?>
