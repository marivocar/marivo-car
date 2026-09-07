<?php
require_once __DIR__ . '/includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT v.*, c.nom AS categorie_nom FROM vehicules v
                        LEFT JOIN categories c ON v.categorie_id = c.id
                        WHERE v.id = :id");
$stmt->execute([':id' => $id]);
$vehicule = $stmt->fetch();

if (!$vehicule) {
    header('Location: vehicules.php');
    exit;
}

$page_title = $vehicule['marque'] . ' ' . $vehicule['modele'];
$active = 'vehicules';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:40px;">
    <div class="container">
        <a href="vehicules.php" class="back-link">&larr; Retour aux véhicules</a>

        <p class="eyebrow"><?= htmlspecialchars($vehicule['categorie_nom'] ?? 'Véhicule') ?></p>
        <h2 style="margin-bottom:26px;"><?= htmlspecialchars($vehicule['marque'].' '.$vehicule['modele']) ?></h2>

        <div class="detail-grid">
            <div class="detail-gallery">
                <div class="detail-gallery-wrap">
                    <span class="car-card-tag"><?= htmlspecialchars($vehicule['categorie_nom'] ?? 'Véhicule') ?></span>
                    <img src="assets/uploads/cars/<?= htmlspecialchars($vehicule['image']) ?>" alt="<?= htmlspecialchars($vehicule['marque'].' '.$vehicule['modele']) ?>"
                         onerror="this.src='assets/images/default-car.jpg'">
                </div>

                <div class="spec-grid">
                    <div class="spec-item">
                        <span class="ic"><?= icon('calendar', 18) ?></span>
                        <div><span class="spec-label">Année</span><span class="spec-value"><?= (int)$vehicule['annee'] ?></span></div>
                    </div>
                    <div class="spec-item">
                        <span class="ic"><?= icon('refresh', 18) ?></span>
                        <div><span class="spec-label">Transmission</span><span class="spec-value"><?= htmlspecialchars($vehicule['transmission']) ?></span></div>
                    </div>
                    <div class="spec-item">
                        <span class="ic"><?= icon('fuel', 18) ?></span>
                        <div><span class="spec-label">Carburant</span><span class="spec-value"><?= htmlspecialchars($vehicule['carburant']) ?></span></div>
                    </div>
                    <div class="spec-item">
                        <span class="ic"><?= icon('users', 18) ?></span>
                        <div><span class="spec-label">Places</span><span class="spec-value"><?= (int)$vehicule['nb_places'] ?> personnes</span></div>
                    </div>
                </div>

                <h3 style="font-size:1.1rem; margin-bottom:10px;">Description</h3>
                <p style="color:var(--text-muted);"><?= nl2br(htmlspecialchars($vehicule['description'])) ?></p>
            </div>

            <div class="form-card booking-card">
                <p class="eyebrow">Tarif</p>
                <div class="price-row">
                    <strong><?= number_format($vehicule['prix_jour'], 0) ?> MAD</strong>
                    <span style="color:var(--text-muted); font-size:0.9rem;">/ jour</span>
                </div>
                <?php if ($vehicule['disponible']): ?>
                    <a href="reservation.php?vehicule_id=<?= $vehicule['id'] ?>" class="btn btn-gold" style="width:100%; justify-content:center;"><?= icon('chat', 16) ?> Réserver ce véhicule</a>
                <?php else: ?>
                    <p class="alert alert-error">Ce véhicule n'est pas disponible actuellement.</p>
                <?php endif; ?>
                <p style="margin-top:14px; font-size:0.85rem; color:var(--text-muted); display:flex; align-items:center; gap:6px;">
                    <?= icon('phone', 14) ?> Besoin d'aide ? <strong>+212 6 00 00 00 00</strong>
                </p>

                <ul class="trust-list">
                    <li><span class="ic"><?= icon('shield', 16) ?></span> Assurance tous risques incluse</li>
                    <li><span class="ic"><?= icon('check', 16) ?></span> Annulation gratuite jusqu'à 48h avant</li>
                    <li><span class="ic"><?= icon('headset', 16) ?></span> Assistance disponible 24h/24</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>