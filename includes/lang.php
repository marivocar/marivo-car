<?php
/**
 * Système de traduction (FR / EN / AR)
 * Utilisation dans les pages : <?= t('nav_accueil') ?>
 */

$langues_disponibles = ['fr', 'en', 'ar'];

// --- Changement de langue via ?lang=xx ---
if (isset($_GET['lang']) && in_array($_GET['lang'], $langues_disponibles)) {
    $_SESSION['lang'] = $_GET['lang'];
}

// --- Langue active (session, sinon français par défaut) ---
$LANGUE = $_SESSION['lang'] ?? 'fr';
if (!in_array($LANGUE, $langues_disponibles)) $LANGUE = 'fr';

// --- Chargement du fichier de traduction correspondant ---
$GLOBALS['TRADUCTIONS'] = require __DIR__ . '/lang/' . $LANGUE . '.php';

/**
 * Retourne la traduction pour une clé donnée.
 * Si la clé n'existe pas, on affiche la clé elle-même (facile à repérer).
 */
function t($cle) {
    return $GLOBALS['TRADUCTIONS'][$cle] ?? $cle;
}

/**
 * Sens d'écriture de la langue active ('rtl' pour l'arabe, 'ltr' sinon).
 */
function langue_direction() {
    return $GLOBALS['LANGUE'] === 'ar' ? 'rtl' : 'ltr';
}

/**
 * Construit un lien vers la page actuelle avec la langue demandée.
 */
function lien_langue($code) {
    return basename($_SERVER['PHP_SELF']) . '?lang=' . $code;
}