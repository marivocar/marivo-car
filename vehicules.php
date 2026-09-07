<?php
require_once __DIR__ . '/includes/db.php';

$page_title = 'Nos véhicules';
$active = 'vehicules';

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();

// --- Construction dynamique de la requête selon les filtres ---
$where = ['v.disponible = 1'];
$params = [];

if (!empty($_GET['categorie'])) {
    $where[] = 'v.categorie_id = :categorie';
    $params[':categorie'] = $_GET['categorie'];
}
if (!empty($_GET['prix_max'])) {
    $where[] = 'v.prix_jour <= :prix_max';
    $params[':prix_max'] = $_GET['prix_max'];
}
if (!empty($_GET['transmission'])) {
    $where[] = 'v.transmission = :transmission';
    $params[':transmission'] = $_GET['transmission'];
}

$sql = "SELECT v.*, c.nom AS categorie_nom FROM vehicules v
        LEFT JOIN categories c ON v.categorie_id = c.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY v.prix_jour ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vehicules = $stmt->fetchAll();

$filtres_actifs = !empty($_GET['categorie']) || !empty($_GET['prix_max']) || !empty($_GET['transmission']);

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:46px;">
    <div class="container">
        <div class="section-head">
            <p class="eyebrow">Catalogue</p>
            <h2>Nos véhicules</h2>
            <p style="color:var(--text-muted); max-width:520px; margin-top:10px;">
                Parcourez notre flotte et filtrez selon vos besoins : catégorie, transmission ou budget.
            </p>
            <div class="speed-divider"><span class="dot"></span></div>
        </div>

        <div class="vehicles-layout">
            <!-- Sidebar de filtres -->
            <aside class="filters-sidebar">
                <h3><?= icon('card', 18) ?> Filtres</h3>
                <form action="vehicules.php" method="GET">
                    <div class="form-group">
                        <label for="categorie"><?= icon('car', 14) ?> Catégorie</label>
                        <select name="categorie" id="categorie">
                            <option value="">Toutes</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (($_GET['categorie'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transmission"><?= icon('refresh', 14) ?> Transmission</label>
                        <select name="transmission" id="transmission">
                            <option value="">Indifférent</option>
                            <option value="Manuelle" <?= (($_GET['transmission'] ?? '') === 'Manuelle') ? 'selected' : '' ?>>Manuelle</option>
                            <option value="Automatique" <?= (($_GET['transmission'] ?? '') === 'Automatique') ? 'selected' : '' ?>>Automatique</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="prix_max"><?= icon('card', 14) ?> Budget max / jour (MAD)</label>
                        <input type="number" name="prix_max" id="prix_max" value="<?= htmlspecialchars($_GET['prix_max'] ?? '') ?>" placeholder="Ex: 500">
                    </div>
                    <button type="submit" class="btn btn-gold">Appliquer les filtres</button>
                    <?php if ($filtres_actifs): ?>
                        <a href="vehicules.php" class="btn-reset">Réinitialiser les filtres</a>
                    <?php endif; ?>
                </form>
            </aside>

            <!-- Résultats -->
            <div>
                <p class="results-count"><strong><?= count($vehicules) ?></strong> véhicule<?= count($vehicules) > 1 ? 's' : '' ?> disponible<?= count($vehicules) > 1 ? 's' : '' ?></p>

                <?php if ($vehicules): ?>
                <div class="cars-grid">
                    <?php foreach ($vehicules as $v): ?>
                    <div class="car-card">
                        <div class="car-card-img">
                            <span class="car-card-tag"><?= htmlspecialchars($v['categorie_nom'] ?? 'Véhicule') ?></span>
                            <img src="assets/uploads/cars/<?= htmlspecialchars($v['image']) ?>" alt="<?= htmlspecialchars($v['marque'].' '.$v['modele']) ?>"
                                 onerror="this.src='assets/images/default-car.jpg'">
                        </div>
                        <div class="car-card-body">
                            <h3><?= htmlspecialchars($v['marque'].' '.$v['modele']) ?></h3>
                            <div class="car-meta">
                                <span><?= icon('car', 15) ?> <?= htmlspecialchars($v['transmission']) ?></span>
                                <span><?= icon('fuel', 15) ?> <?= htmlspecialchars($v['carburant']) ?></span>
                                <span><?= icon('users', 15) ?> <?= (int)$v['nb_places'] ?> places</span>
                            </div>
                            <div class="car-price">
                                <div><strong><?= number_format($v['prix_jour'], 0) ?> MAD</strong><br><small>par jour</small></div>
                                <a href="vehicule-detail.php?id=<?= $v['id'] ?>" class="btn btn-gold btn-sm">Réserver</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <div class="form-card" style="text-align:center;">
                        <p>Aucun véhicule ne correspond à votre recherche. Essayez d'élargir vos critères.</p>
                        <a href="vehicules.php" class="btn btn-outline" style="margin-top:14px; border-color:var(--orange-dark); color:var(--text-dark);">Réinitialiser les filtres</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>