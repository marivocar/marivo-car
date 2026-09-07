<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page_title = 'Messages';
$admin_active = 'messages';

if (isset($_GET['lu'])) {
    $pdo->prepare("UPDATE messages SET lu = 1 WHERE id = :id")->execute([':id' => (int)$_GET['lu']]);
    header('Location: messages.php');
    exit;
}
if (isset($_GET['supprimer'])) {
    $pdo->prepare("DELETE FROM messages WHERE id = :id")->execute([':id' => (int)$_GET['supprimer']]);
    header('Location: messages.php');
    exit;
}

$messages = $pdo->query("SELECT * FROM messages ORDER BY date_envoi DESC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h2 style="font-size:1.4rem;">Messages reçus</h2>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>De</th>
            <th>Sujet</th>
            <th>Message</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($messages): foreach ($messages as $m): ?>
        <tr style="<?= $m['lu'] ? '' : 'font-weight:600;' ?>">
            <td><?= htmlspecialchars($m['nom']) ?><br><small><?= htmlspecialchars($m['email']) ?></small></td>
            <td><?= htmlspecialchars($m['sujet'] ?: '—') ?></td>
            <td style="max-width:320px;"><?= nl2br(htmlspecialchars(mb_strimwidth($m['message'], 0, 140, '…'))) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($m['date_envoi'])) ?></td>
            <td>
                <?php if (!$m['lu']): ?><a href="messages.php?lu=<?= $m['id'] ?>" class="btn btn-dark btn-sm">Marquer lu</a><?php endif; ?>
                <a href="messages.php?supprimer=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce message ?');">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="5">Aucun message.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>