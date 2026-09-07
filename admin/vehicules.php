<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page_title = 'Véhicules';
$admin_active = 'vehicules';

// --- Suppression ---
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $pdo->prepare("DELETE FROM vehicules WHERE id = :id")->execute([':id' => $id]);
    header('Location: vehicules.php?msg=supprime');
    exit;
}

$vehicules = $pdo->query("SELECT v.*, c.nom AS categorie_nom FROM vehicules v
                           LEFT JOIN categories c ON v.categorie_id = c.id
                           ORDER BY v.date_ajout DESC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h2 style="font-size:1.4rem;">Gestion des véhicules</h2>
    <a href="vehicule-form.php" class="btn btn-gold btn-sm">+ Ajouter un véhicule</a>
</div>

<?php if (($_GET['msg'] ?? '') === 'supprime'): ?>
    <div class="alert alert-success">Véhicule supprimé avec succès.</div>
<?php elseif (($_GET['msg'] ?? '') === 'enregistre'): ?>
    <div class="alert alert-success">Véhicule enregistré avec succès.</div>
<?php endif; ?>

<table class="admin-table">
    <thead>
        <tr>
            <th>Véhicule</th>
            <th>Catégorie</th>
            <th>Prix / jour</th>
            <th>Disponibilité</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($vehicules): foreach ($vehicules as $v): ?>
        <tr>
            <td><?= htmlspecialchars($v['marque'].' '.$v['modele']) ?> (<?= (int)$v['annee'] ?>)</td>
            <td><?= htmlspecialchars($v['categorie_nom'] ?? '—') ?></td>
            <td><?= number_format($v['prix_jour'], 0) ?> MAD</td>
            <td>
                <span class="badge <?= $v['disponible'] ? 'badge-confirmee' : 'badge-annulee' ?>">
                    <?= $v['disponible'] ? 'Disponible' : 'Indisponible' ?>
                </span>
            </td>
            <td>
                <a href="vehicule-form.php?id=<?= $v['id'] ?>" class="btn btn-dark btn-sm">Modifier</a>
                <a href="vehicules.php?supprimer=<?= $v['id'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Supprimer ce véhicule ? Cette action est irréversible.');">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="5">Aucun véhicule enregistré.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>