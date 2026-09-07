<?php
require_once __DIR__ . '/includes/auth.php';
if (est_lecteur()) {
    header('Location: dashboard.php');
    exit;
}

$admin_page_title = 'Utilisateurs';
$admin_active = 'utilisateurs';

$erreurs = [];
$succes = false;

// --- Changement de statut (approuver / refuser) ---
if (isset($_GET['action'], $_GET['id'])) {
    $idCible = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approuver') {
        $pdo->prepare("UPDATE admins SET statut = 'Approuvé' WHERE id = :id")->execute([':id' => $idCible]);
        header('Location: utilisateurs.php?msg=approuve');
        exit;
    }
    if ($action === 'refuser') {
        $pdo->prepare("UPDATE admins SET statut = 'Refusé' WHERE id = :id")->execute([':id' => $idCible]);
        header('Location: utilisateurs.php?msg=refuse');
        exit;
    }
}

// --- Suppression (impossible de se supprimer soi-même, ni de supprimer le dernier admin approuvé) ---
if (isset($_GET['supprimer'])) {
    $idSuppr = (int)$_GET['supprimer'];
    $nbApprouves = $pdo->query("SELECT COUNT(*) FROM admins WHERE statut = 'Approuvé'")->fetchColumn();

    if ($idSuppr === (int)$_SESSION['admin_id']) {
        header('Location: utilisateurs.php?msg=erreur_soi_meme');
        exit;
    }
    if ($nbApprouves <= 1) {
        header('Location: utilisateurs.php?msg=erreur_dernier');
        exit;
    }
    $pdo->prepare("DELETE FROM admins WHERE id = :id")->execute([':id' => $idSuppr]);
    header('Location: utilisateurs.php?msg=supprime');
    exit;
}

// --- Ajout direct d'un nouvel utilisateur (déjà approuvé, créé par un admin) ---
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
            $erreurs[] = "Ce nom d'utilisateur existe déjà.";
        }
    }

    if (empty($erreurs)) {
        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (nom_utilisateur, mot_de_passe, email, statut) VALUES (:u, :p, :e, 'Approuvé')");
        $stmt->execute([':u' => $nom_utilisateur, ':p' => $hash, ':e' => $email]);
        $succes = true;
    }
}

$enAttente = $pdo->query("SELECT * FROM admins WHERE statut = 'En attente' ORDER BY date_creation ASC")->fetchAll();
$autres = $pdo->query("SELECT * FROM admins WHERE statut != 'En attente' ORDER BY date_creation ASC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h2 style="font-size:1.4rem;">Utilisateurs administrateurs</h2>
</div>

<?php if ($succes): ?>
    <div class="alert alert-success">Utilisateur créé avec succès.</div>
<?php endif; ?>
<?php if (($_GET['msg'] ?? '') === 'approuve'): ?>
    <div class="alert alert-success">Demande approuvée. L'utilisateur peut maintenant se connecter.</div>
<?php elseif (($_GET['msg'] ?? '') === 'refuse'): ?>
    <div class="alert alert-success">Demande refusée.</div>
<?php elseif (($_GET['msg'] ?? '') === 'supprime'): ?>
    <div class="alert alert-success">Utilisateur supprimé avec succès.</div>
<?php elseif (($_GET['msg'] ?? '') === 'erreur_soi_meme'): ?>
    <div class="alert alert-error">Vous ne pouvez pas supprimer votre propre compte.</div>
<?php elseif (($_GET['msg'] ?? '') === 'erreur_dernier'): ?>
    <div class="alert alert-error">Impossible de supprimer le dernier administrateur approuvé.</div>
<?php endif; ?>
<?php if ($erreurs): ?>
    <div class="alert alert-error"><ul style="padding-left:18px;"><?php foreach ($erreurs as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div>
<?php endif; ?>

<?php if ($enAttente): ?>
<h3 style="font-size:1.05rem; margin-bottom:14px;">Demandes en attente (<?= count($enAttente) ?>)</h3>
<table class="admin-table" style="margin-bottom:34px;">
    <thead>
        <tr>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Demandé le</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($enAttente as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['nom_utilisateur']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($a['date_creation'])) ?></td>
            <td>
                <a href="utilisateurs.php?action=approuver&id=<?= $a['id'] ?>" class="btn btn-gold btn-sm">Approuver</a>
                <a href="utilisateurs.php?action=refuser&id=<?= $a['id'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Refuser cette demande ?');">Refuser</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<div style="display:grid; grid-template-columns: 1fr 380px; gap:24px; align-items:start;">
    <div>
        <h3 style="font-size:1.05rem; margin-bottom:14px;">Utilisateurs</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom d'utilisateur</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($autres as $a): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($a['nom_utilisateur']) ?>
                        <?php if ((int)$a['id'] === (int)$_SESSION['admin_id']): ?>
                            <span class="badge badge-confirmee">Vous</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                    <td>
                        <span class="badge <?= $a['statut'] === 'Approuvé' ? 'badge-confirmee' : 'badge-annulee' ?>"><?= htmlspecialchars($a['statut']) ?></span>
                    </td>
                    <td>
                        <?php if ((int)$a['id'] !== (int)$_SESSION['admin_id']): ?>
                            <a href="utilisateurs.php?supprimer=<?= $a['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-card">
        <h3 style="font-size:1rem; margin-bottom:18px;">Créer directement un utilisateur</h3>
        <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:16px;">
            Ce compte sera approuvé automatiquement. Pour laisser quelqu'un demander l'accès lui-même,
            partagez-lui le lien <code>admin/demande-acces.php</code>.
        </p>
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
            <button type="submit" class="btn btn-gold" style="width:100%;">Créer l'utilisateur</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>