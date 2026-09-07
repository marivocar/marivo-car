<?php
/**
 * Configuration + connexion à la base de données — tout en un seul fichier.
 * Modifiez les constantes ci-dessous selon votre environnement.
 */

// --- Base de données ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'marivo_car');
define('DB_USER', 'root');   // à adapter (souvent 'root' sur WAMP/XAMPP)
define('DB_PASS', '');       // à adapter

// --- Site ---
define('SITE_NAME', 'Marivo Car');
define('SITE_URL', 'http://localhost/marivo_car'); // à adapter selon votre dossier

// --- Session (avant tout affichage HTML) ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Fuseau horaire ---
date_default_timezone_set('Africa/Casablanca');

// --- Erreurs visibles en développement (à désactiver en production) ---
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Connexion PDO ---
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données. Vérifiez includes/db.php. (' . $e->getMessage() . ')');
}
// --- Icônes SVG réutilisables ---
require_once __DIR__ . '/icons.php';
// --- Icônes SVG réutilisables ---
require_once __DIR__ . '/icons.php';



