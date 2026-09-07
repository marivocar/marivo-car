<?php
require_once __DIR__ . '/../includes/db.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['nom_utilisateur'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE nom_utilisateur = :u LIMIT 1");
    $stmt->execute([':u' => $identifiant]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($mdp, $admin['mot_de_passe'])) {
        if ($admin['statut'] === 'En attente') {
            $erreur = "Votre compte est en attente d'approbation par un administrateur.";
        } elseif ($admin['statut'] === 'Refusé') {
            $erreur = "Votre demande d'accès a été refusée.";
        } else {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nom'] = $admin['nom_utilisateur'];
            $_SESSION['admin_role'] = $admin['role'];
            header('Location: dashboard.php');
            exit;
        }
    } else {
        $erreur = "Identifiant ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion admin | <?= SITE_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>

<div class="login-page">
    <div class="login-brand-panel">
        <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Marivo Car">
        <h1>Gérez votre <span>flotte</span> en toute simplicité</h1>
        <p class="lead">Accédez au tableau de bord pour gérer vos véhicules, suivre vos réservations et répondre à vos clients.</p>
        <ul class="login-features">
            <li><span class="ic"><?= icon('car', 18) ?></span> Gestion de la flotte de véhicules</li>
            <li><span class="ic"><?= icon('calendar', 18) ?></span> Suivi des réservations en temps réel</li>
            <li><span class="ic"><?= icon('chart', 18) ?></span> Statistiques et revenus</li>
            <li><span class="ic"><?= icon('shield', 18) ?></span> Accès sécurisé et contrôlé</li>
        </ul>
    </div>

    <div class="login-form-panel">
        <div class="login-form-box">
            <p class="eyebrow">Marivo Car</p>
            <h2>Espace administrateur</h2>
            <p class="sub">Connectez-vous pour accéder à votre tableau de bord.</p>

            <?php if ($erreur): ?>
                <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label><?= icon('users', 14) ?> Nom d'utilisateur</label>
                    <input type="text" name="nom_utilisateur" required autofocus>
                </div>
                <div class="form-group">
                    <label><?= icon('shield', 14) ?> Mot de passe</label>
                    <input type="password" name="mot_de_passe" required>
                </div>
                <button type="submit" class="btn btn-gold"><?= icon('check', 16) ?> Se connecter</button>
            </form>

            <p class="signup-link">Pas encore de compte ? <a href="demande-acces.php">Demander un accès</a></p>
        </div>
    </div>
</div>

</body>
</html>