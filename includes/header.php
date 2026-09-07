<?php
// $page_title et $active doivent être définis dans la page qui inclut ce header
if (!isset($page_title)) $page_title = SITE_NAME;
if (!isset($active)) $active = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?> | <?= SITE_NAME ?></title>
<meta name="description" content="Marivo Car - Location de voitures au Maroc. Large choix de véhicules, réservation en ligne simple et rapide.">
<link rel="icon" href="<?= SITE_URL ?>/assets/images/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container">
        <div style="display:flex; align-items:center; gap:12px;">
            <a href="<?= SITE_URL ?>/index.php" class="logo-link">
                <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Marivo Car - Location de voiture">
            </a>
            <span class="city-badge">Marrakech</span>
        </div>

        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="<?= SITE_URL ?>/index.php" class="<?= $active === 'accueil' ? 'active' : '' ?>">Accueil</a></li>
                <li><a href="<?= SITE_URL ?>/vehicules.php" class="<?= $active === 'vehicules' ? 'active' : '' ?>">Nos véhicules</a></li>
                <li><a href="<?= SITE_URL ?>/a-propos.php" class="<?= $active === 'apropos' ? 'active' : '' ?>">À propos</a></li>
                <li><a href="<?= SITE_URL ?>/contact.php" class="<?= $active === 'contact' ? 'active' : '' ?>">Contact</a></li>
            </ul>
        </nav>

        <div class="header-cta">
            <span class="pill-tag" title="Langue"><?= icon('globe', 14) ?> FR</span>
            <a href="tel:+212600000000" class="icon-btn" aria-label="Appeler Marivo Car"><?= icon('phone', 16) ?></a>
            <a href="<?= SITE_URL ?>/vehicules.php" class="btn btn-gold btn-sm">Réserver maintenant</a>
            <button class="nav-toggle" id="navToggle" aria-label="Menu">☰</button>
        </div>
    </div>
</header>