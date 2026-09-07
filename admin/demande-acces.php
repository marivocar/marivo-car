<?php
require_once __DIR__ . '/../includes/db.php';

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';
    $mdp_confirm = $_POST['mot_de_passe_confirm'] ?? '';

    if ($nom_utilisateur === '') $erreurs[] = "Le nom d'utilisateur est requis.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "Email invalide.";
    if (strlen($mdp) < 8) $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
    if ($mdp !== $mdp_confirm) $erreurs[] = "Les mots de passe ne correspondent pas.";

    if (empty($erreurs)) {
        $existe = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE nom_utilisateur = :u");
        $existe->execute([':u' => $nom_utilisateur]);
        if ($existe->fetchColumn() > 0) {
            $erreurs[] = "Ce nom d'utilisateur est déjà pris.";
        }
    }

    if (empty($erreurs)) {
        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (nom_utilisateur, mot_de_passe, email, statut) VALUES (:u, :p, :e, 'En attente')");
        $stmt->execute([':u' => $nom_utilisateur, ':p' => $hash, ':e' => $email]);
        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Demande d'accès admin | <?= SITE_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-box" style="width:440px; text-align:left;">
        <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Marivo Car" style="margin:0 auto 20px;">

        <?php if ($succes): ?>
            <div class="alert alert-success">
                <strong>Votre demande a bien été envoyée !</strong><br>
                Un administrateur doit approuver votre compte avant que vous puissiez vous connecter.
            </div>
            <a href="login.php" class="btn btn-outline" style="width:100%; text-align:center; display:block; border-color:var(--stone);">Retour à la connexion</a>
        <?php else: ?>
            <h3 style="margin-bottom:8px; font-size:1.1rem;">Demander un accès admin</h3>
            <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:18px;">
                Votre demande sera examinée par un administrateur avant activation.
            </p>
            <?php if ($erreurs): ?>
                <div class="alert alert-error"><ul style="padding-left:18px;"><?php foreach ($erreurs as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="nom_utilisateur" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe (8 caractères min.)</label>
                    <input type="password" name="mot_de_passe" required>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="mot_de_passe_confirm" required>
                </div>
                <button type="submit" class="btn btn-gold" style="width:100%;">Envoyer la demande</button>
            </form>
            <p style="text-align:center; margin-top:16px; font-size:0.85rem;">
                <a href="login.php" style="color:var(--text-muted);">← Retour à la connexion</a>
            </p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>