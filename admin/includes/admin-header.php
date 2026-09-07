<?php if (!isset($admin_page_title)) $admin_page_title = 'Tableau de bord'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($admin_page_title) ?> | Admin <?= SITE_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="dashboard.php" class="logo-link">
            <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Marivo Car" style="height:44px;">
        </a>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="<?= ($admin_active ?? '') === 'dashboard' ? 'active' : '' ?>"><?= icon('chart', 18) ?> <span class="label-text">Tableau de bord</span></a></li>
                <li><a href="vehicules.php" class="<?= ($admin_active ?? '') === 'vehicules' ? 'active' : '' ?>"><?= icon('car', 18) ?> <span class="label-text">Véhicules</span></a></li>
                <li><a href="reservations.php" class="<?= ($admin_active ?? '') === 'reservations' ? 'active' : '' ?>"><?= icon('calendar', 18) ?> <span class="label-text">Réservations</span></a></li>
                <li><a href="messages.php" class="<?= ($admin_active ?? '') === 'messages' ? 'active' : '' ?>"><?= icon('mail', 18) ?> <span class="label-text">Messages</span></a></li>
                <li><a href="avis.php" class="<?= ($admin_active ?? '') === 'avis' ? 'active' : '' ?>"><?= icon('star', 18) ?> <span class="label-text">Avis clients</span></a></li>
                <li><a href="utilisateurs.php" class="<?= ($admin_active ?? '') === 'utilisateurs' ? 'active' : '' ?>"><?= icon('users', 18) ?> <span class="label-text">Utilisateurs</span></a></li>
<li><a href="<?= SITE_URL ?>/index.php" target="_blank"><?= icon('globe', 18) ?> <span class="label-text">Voir le site</span></a></li>
                <li><a href="logout.php"><?= icon('logout', 18) ?> <span class="label-text">Déconnexion</span></a></li>
            </ul>
        </nav>
    </aside>
    <main class="admin-main">