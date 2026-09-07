<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page_title = 'Réservations';
$admin_active = 'reservations';

// --- Mise à jour du statut ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'], $_POST['statut'])) {
    $statutsValides = ['En attente', 'Confirmée', 'Annulée', 'Terminée'];
    if (in_array($_POST['statut'], $statutsValides)) {
        $stmt = $pdo->prepare("UPDATE reservations SET statut = :statut WHERE id = :id");
        $stmt->execute([':statut' => $_POST['statut'], ':id' => (int)$_POST['reservation_id']]);
    }
    header('Location: reservations.php');
    exit;
}

$reservations = $pdo->query("SELECT r.*, v.marque, v.modele FROM reservations r
                              JOIN vehicules v ON r.vehicule_id = v.id
                              ORDER BY r.date_reservation DESC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';

$badgeClass = ['En attente'=>'badge-attente','Confirmée'=>'badge-confirmee','Annulée'=>'badge-annulee','Terminée'=>'badge-terminee'];
?>

<div class="admin-topbar">
    <h2 style="font-size:1.4rem;">Réservations</h2>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Client</th>
            <th>Contact</th>
            <th>Véhicule</th>
            <th>Période</th>
            <th>Total</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($reservations): foreach ($reservations as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['nom_client']) ?></td>
            <td><?= htmlspecialchars($r['telephone_client']) ?><br><small><?= htmlspecialchars($r['email_client']) ?></small></td>
            <td><?= htmlspecialchars($r['marque'].' '.$r['modele']) ?></td>
            <td><?= date('d/m/Y', strtotime($r['date_debut'])) ?> → <?= date('d/m/Y', strtotime($r['date_fin'])) ?></td>
            <td><?= number_format($r['prix_total'], 0) ?> MAD</td>
            <td>
                <form method="POST" style="display:flex; gap:6px; align-items:center;">
                    <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                    <select name="statut" onchange="this.form.submit()" class="badge <?= $badgeClass[$r['statut']] ?? '' ?>" style="border:none; cursor:pointer;">
                        <?php foreach (array_keys($badgeClass) as $s): ?>
                            <option value="<?= $s ?>" <?= $r['statut']===$s?'selected':'' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6">Aucune réservation.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>