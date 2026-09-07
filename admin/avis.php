<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page_title = 'Avis clients';
$admin_active = 'avis';

// --- Changement de statut ---
if (isset($_GET['action'], $_GET['id'])) {
    if (est_lecteur()) {
        header('Location: avis.php?msg=acces_refuse');
        exit;
    }
    $idCible = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approuver') {
        $pdo->prepare("UPDATE avis SET statut = 'Approuvé' WHERE id = :id")->execute([':id' => $idCible]);
    } elseif ($action === 'refuser') {
        $pdo->prepare("UPDATE avis SET statut = 'Refusé' WHERE id = :id")->execute([':id' => $idCible]);
    } elseif ($action === 'supprimer') {
        $pdo->prepare("DELETE FROM avis WHERE id = :id")->execute([':id' => $idCible]);
    }
    header('Location: avis.php?msg=maj');
    exit;
}

$enAttente = $pdo->query("SELECT * FROM avis WHERE statut = 'En attente' ORDER BY date_creation ASC")->fetchAll();
$autres = $pdo->query("SELECT * FROM avis WHERE statut != 'En attente' ORDER BY date_creation DESC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';

$badgeClass = ['En attente'=>'badge-attente','Approuvé'=>'badge-confirmee','Refusé'=>'badge-annulee'];
?>

<div class="admin-topbar">
    <h2 style="font-size:1.4rem;">Avis clients</h2>
</div>

<?php if (($_GET['msg'] ?? '') === 'maj'): ?>
    <div class="alert alert-success">Mise à jour effectuée avec succès.</div>
<?php elseif (($_GET['msg'] ?? '') === 'acces_refuse'): ?>
    <div class="alert alert-error">Accès refusé : votre compte est en lecture seule.</div>
<?php endif; ?>

<?php if ($enAttente): ?>
<h3 style="font-size:1.05rem; margin-bottom:14px;">En attente de modération (<?= count($enAttente) ?>)</h3>
<div style="display:flex; flex-direction:column; gap:14px; margin-bottom:34px;">
    <?php foreach ($enAttente as $a): ?>
    <div class="form-card" style="padding:20px;">
        <div style="display:flex; justify-content:space-between; align-items:start; gap:14px;">
            <div>
                <div style="font-weight:600; margin-bottom:2px;"><?= htmlspecialchars($a['nom']) ?> <span style="color:var(--text-muted); font-weight:400; font-size:0.85rem;">— <?= htmlspecialchars($a['ville'] ?: 'ville non précisée') ?></span></div>
                <div style="color:var(--orange); margin-bottom:8px;"><?= str_repeat('★', $a['note']) . str_repeat('☆', 5 - $a['note']) ?></div>
                <p style="font-size:0.9rem; color:var(--text-dark);">"<?= htmlspecialchars($a['texte']) ?>"</p>
                <p style="font-size:0.75rem; color:var(--text-muted); margin-top:8px;"><?= date('d/m/Y H:i', strtotime($a['date_creation'])) ?></p>
            </div>
            <div style="display:flex; gap:8px; flex-shrink:0;">
                <?php if (!est_lecteur()): ?>
                    <a href="avis.php?action=approuver&id=<?= $a['id'] ?>" class="btn btn-gold btn-sm">Approuver</a>
                    <a href="avis.php?action=refuser&id=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Refuser cet avis ?');">Refuser</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<h3 style="font-size:1.05rem; margin-bottom:14px;">Tous les avis</h3>
<table class="admin-table">
    <thead>
        <tr>
            <th>Client</th>
            <th>Note</th>
            <th>Avis</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($autres): foreach ($autres as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['nom']) ?><br><small><?= htmlspecialchars($a['ville'] ?: '—') ?></small></td>
            <td style="color:var(--orange); white-space:nowrap;"><?= str_repeat('★', $a['note']) ?></td>
            <td style="max-width:320px;"><?= htmlspecialchars(mb_strimwidth($a['texte'], 0, 100, '…')) ?></td>
            <td><span class="badge <?= $badgeClass[$a['statut']] ?? '' ?>"><?= htmlspecialchars($a['statut']) ?></span></td>
            <td>
                <?php if (!est_lecteur()): ?>
                    <a href="avis.php?action=supprimer&id=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet avis ?');">Supprimer</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="5">Aucun avis traité pour le moment.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>