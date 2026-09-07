<?php
require_once __DIR__ . '/includes/auth.php';

$admin_page_title = 'Tableau de bord';
$admin_active = 'dashboard';

$nb_vehicules = $pdo->query("SELECT COUNT(*) FROM vehicules")->fetchColumn();
$nb_dispo = $pdo->query("SELECT COUNT(*) FROM vehicules WHERE disponible = 1")->fetchColumn();
$nb_reservations = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$nb_attente = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'En attente'")->fetchColumn();
$nb_messages = $pdo->query("SELECT COUNT(*) FROM messages WHERE lu = 0")->fetchColumn();

$dernieres = $pdo->query("SELECT r.*, v.marque, v.modele FROM reservations r
                           JOIN vehicules v ON r.vehicule_id = v.id
                           ORDER BY r.date_reservation DESC LIMIT 6")->fetchAll();

// --- Revenus des 6 derniers mois (réservations confirmées ou terminées) ---
$moisAbreges = [1=>'Jan',2=>'Fév',3=>'Mar',4=>'Avr',5=>'Mai',6=>'Jun',7=>'Jul',8=>'Aoû',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Déc'];
$moisLabels = [];
$moisData = [];
for ($i = 5; $i >= 0; $i--) {
    $timestamp = strtotime("-$i months");
    $cle = date('Y-m', $timestamp);
    $moisLabels[$cle] = $moisAbreges[(int)date('n', $timestamp)];
    $moisData[$cle] = 0;
}
$revenusParMois = $pdo->query("SELECT DATE_FORMAT(date_debut, '%Y-%m') AS mois, SUM(prix_total) AS total
                                FROM reservations
                                WHERE statut IN ('Confirmée', 'Terminée')
                                GROUP BY mois")->fetchAll();
foreach ($revenusParMois as $row) {
    if (isset($moisData[$row['mois']])) $moisData[$row['mois']] = (float)$row['total'];
}
$maxRevenu = max(1, max($moisData));

// --- Véhicules les plus réservés ---
$topVehicules = $pdo->query("SELECT v.marque, v.modele, COUNT(r.id) AS nb
                              FROM reservations r
                              JOIN vehicules v ON r.vehicule_id = v.id
                              GROUP BY r.vehicule_id
                              ORDER BY nb DESC LIMIT 4")->fetchAll();

// --- Derniers messages reçus ---
$derniersMessages = $pdo->query("SELECT * FROM messages ORDER BY date_envoi DESC LIMIT 4")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h2 style="font-size:1.4rem;">Bonjour, <?= htmlspecialchars($_SESSION['admin_nom']) ?> 👋</h2>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="num"><?= $nb_vehicules ?></div>
        <div class="label">Véhicules au total</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= $nb_dispo ?></div>
        <div class="label">Véhicules disponibles</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= $nb_reservations ?></div>
        <div class="label">Réservations totales</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= $nb_attente ?></div>
        <div class="label">En attente de confirmation</div>
    </div>
    <div class="stat-card">
        <div class="num"><?= $nb_messages ?></div>
        <div class="label">Messages non lus</div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="chart-card">
        <h3 style="font-size:1.05rem; margin-bottom:20px;">Revenus des 6 derniers mois</h3>
        <div class="chart-bars">
            <?php foreach ($moisData as $cle => $valeur): ?>
                <div class="chart-bar-col">
                    <div class="chart-bar-track">
                        <div class="chart-bar-fill" style="height: <?= max(4, round($valeur / $maxRevenu * 100)) ?>%;" title="<?= number_format($valeur, 0) ?> MAD"></div>
                    </div>
                    <span class="chart-bar-value"><?= $valeur > 0 ? number_format($valeur, 0) : '0' ?></span>
                    <span class="chart-bar-label"><?= $moisLabels[$cle] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div>
        <div class="form-card" style="margin-bottom:20px;">
            <h3 style="font-size:0.95rem; margin-bottom:16px;"><?= icon('car', 16) ?> Véhicules les plus réservés</h3>
            <?php if ($topVehicules): foreach ($topVehicules as $tv): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid var(--stone-light); font-size:0.85rem;">
                    <span><?= htmlspecialchars($tv['marque'].' '.$tv['modele']) ?></span>
                    <span class="badge badge-confirmee"><?= $tv['nb'] ?> résa</span>
                </div>
            <?php endforeach; else: ?>
                <p style="font-size:0.85rem; color:var(--text-muted);">Aucune donnée pour le moment.</p>
            <?php endif; ?>
        </div>

        <div class="form-card">
            <h3 style="font-size:0.95rem; margin-bottom:16px;"><?= icon('mail', 16) ?> Derniers messages</h3>
            <?php if ($derniersMessages): foreach ($derniersMessages as $m): ?>
                <div style="padding:9px 0; border-bottom:1px solid var(--stone-light);">
                    <div style="display:flex; justify-content:space-between; font-size:0.85rem; font-weight:600;">
                        <span><?= htmlspecialchars($m['nom']) ?></span>
                        <?php if (!$m['lu']): ?><span class="badge badge-attente">Nouveau</span><?php endif; ?>
                    </div>
                    <p style="font-size:0.8rem; color:var(--text-muted); margin-top:3px;"><?= htmlspecialchars(mb_strimwidth($m['message'], 0, 60, '…')) ?></p>
                </div>
            <?php endforeach; else: ?>
                <p style="font-size:0.85rem; color:var(--text-muted);">Aucun message pour le moment.</p>
            <?php endif; ?>
            <a href="messages.php" class="btn btn-outline btn-sm" style="width:100%; justify-content:center; margin-top:14px; border-color:var(--stone);">Voir tous les messages</a>
        </div>
    </div>
</div>

<h3 style="font-size:1.05rem; margin-bottom:14px;">Dernières réservations</h3>
<table class="admin-table">
    <thead>
        <tr>
            <th>Client</th>
            <th>Véhicule</th>
            <th>Période</th>
            <th>Total</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($dernieres): foreach ($dernieres as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['nom_client']) ?></td>
            <td><?= htmlspecialchars($r['marque'].' '.$r['modele']) ?></td>
            <td><?= date('d/m/Y', strtotime($r['date_debut'])) ?> → <?= date('d/m/Y', strtotime($r['date_fin'])) ?></td>
            <td><?= number_format($r['prix_total'], 0) ?> MAD</td>
            <td>
                <?php
                $badgeClass = ['En attente'=>'badge-attente','Confirmée'=>'badge-confirmee','Annulée'=>'badge-annulee','Terminée'=>'badge-terminee'];
                ?>
                <span class="badge <?= $badgeClass[$r['statut']] ?? '' ?>"><?= htmlspecialchars($r['statut']) ?></span>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="5">Aucune réservation pour le moment.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>