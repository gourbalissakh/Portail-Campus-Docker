<?php
/**
 * Déconnexion
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

logoutUser();
setFlashMessage('info', 'Vous avez été déconnecté.');
header('Location: /auth/login.php');
exit;
?>
