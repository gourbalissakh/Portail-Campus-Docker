<?php
/**
 * Fonctions utilitaires
 */

/**
 * Échappe les caractères HTML pour prévenir les attaques XSS
 */
function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Génère un token CSRF
 */
function generateCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie le token CSRF
 */
function verifyCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Affiche un message flash
 */
function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Récupère et supprime le message flash
 */
function getFlashMessage(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Génère un matricule étudiant
 */
function generateMatricule(string $niveau, string $filiere): string {
    $year = date('Y');
    $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
    $filiereCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $filiere), 0, 4));
    return "{$niveau}{$filiereCode}{$year}{$random}";
}

/**
 * Valide un email
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un numéro de téléphone sénégalais
 */
function isValidPhone(string $phone): bool {
    return preg_match('/^(77|78|76|70|75|33)\d{7}$/', $phone);
}
?>
