<?php
/**
 * Supprimer un étudiant
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    setFlashMessage('error', 'Étudiant non trouvé.');
    header('Location: /students/list.php');
    exit;
}

// Récupérer l'étudiant pour afficher son nom
$stmt = $pdo->prepare('SELECT matricule, nom, prenom FROM etudiants WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    setFlashMessage('error', 'Étudiant non trouvé.');
    header('Location: /students/list.php');
    exit;
}

// Supprimer
$deleteStmt = $pdo->prepare('DELETE FROM etudiants WHERE id = ?');
$deleteStmt->execute([$id]);

setFlashMessage('success', "L'étudiant {$student['prenom']} {$student['nom']} ({$student['matricule']}) a été supprimé.");
header('Location: /students/list.php');
exit;
?>
