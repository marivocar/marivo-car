<?php
/**
 * Garde d'authentification — à inclure en haut de chaque page admin protégée
 */
require_once __DIR__ . '/../../includes/db.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

/**
 * Retourne true si l'utilisateur connecté a un accès en lecture seule
 * (ne peut ni ajouter, ni modifier, ni supprimer).
 */
function est_lecteur() {
    return ($_SESSION['admin_role'] ?? 'Lecteur') !== 'Administrateur';
}