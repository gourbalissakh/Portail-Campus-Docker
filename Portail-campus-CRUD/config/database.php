<?php
/**
 * Configuration de la connexion à la base de données
 * Utilise PDO pour une meilleure sécurité
 */

$host = 'db_portail_campus';
$dbname = 'portail_campus_db';
$username = 'campus_user';
$password = 'campus_pass';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log("Erreur de connexion BDD : " . $e->getMessage());
    die("Erreur de connexion à la base de données.");
}
?>
