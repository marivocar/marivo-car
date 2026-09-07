<?php
require_once __DIR__ . '/includes/db.php';

$page_title = 'Accueil';
$active = 'accueil';

// Liste complète des véhicules disponibles (pour le sélecteur de recherche)
$tous_vehicules = $pdo->query("SELECT id, marque, modele FROM vehicules WHERE disponible = 1 ORDER BY marque")->fetchAll();

// Villes et aéroports proposés
$villes = [
    // Villes
    'Marrakech', 'Casablanca', 'Rabat', 'Agadir', 'Fès', 'Tanger',
    'Essaouira', 'Ouarzazate', 'Meknès', 'Oujda', 'Nador',

    // Aéroports du Maroc
    'Aéroport Mohammed V (Casablanca)',
    'Aéroport Marrakech-Ménara',
    'Aéroport Agadir-Al Massira',
    'Aéroport Fès-Saïss',
    'Aéroport Tanger-Ibn Battouta',
    'Aéroport Rabat-Salé',
    'Aéroport Oujda-Angads',
    'Aéroport Nador-Al Aroui',
    'Aéroport Essaouira-Mogador',
    'Aéroport Ouarzazate',
    'Aéroport Al Hoceima-Cherif Al Idrissi',
    'Aéroport Errachidia',
    'Aéroport Béni Mellal',
    'Aéroport Dakhla',
    'Aéroport Laâyoune-Hassan Ier',
];

// Récupérer 6 véhicules disponibles à mettre en avant
$stmt = $pdo->query("SELECT v.*, c.nom AS categorie_nom FROM vehicules v
                      LEFT JOIN categories c ON v.categorie_id = c.id
                      WHERE v.disponible = 1
                      ORDER BY v.date_ajout DESC LIMIT 6");
$vehicules = $stmt->fetchAll();

// --- Traitement du formulaire d'avis client ---
$avis_erreurs = [];
$avis_succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['avis_nom'])) {
    $avis_nom = trim($_POST['avis_nom'] ?? '');
    $avis_ville = trim($_POST['avis_ville'] ?? '');
    $avis_note = (int)($_POST['avis_note'] ?? 5);
    $avis_texte = trim($_POST['avis_texte'] ?? '');

    if ($avis_nom === '') $avis_erreurs[] = "Le nom est requis.";
    if ($avis_texte === '') $avis_erreurs[] = "Le message est requis.";
    if ($avis_note < 1 || $avis_note > 5) $avis_note = 5;

    if (empty($avis_erreurs)) {
        $insert = $pdo->prepare("INSERT INTO avis (nom, ville, note, texte) VALUES (:n, :v, :note, :t)");
        $insert->execute([':n' => $avis_nom, ':v' => $avis_ville, ':note' => $avis_note, ':t' => $avis_texte]);
        $avis_succes = true;
    }
}

// Avis approuvés à afficher publiquement
$avis = $pdo->query("SELECT * FROM avis WHERE statut = 'Approuvé' ORDER BY date_creation DESC LIMIT 3")->fetchAll();require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <p class="eyebrow">Location de voiture au Maroc</p>
                <h1>Location de voiture à <span>Marrakech</span></h1>
                <p>Une flotte soignée, une tarification claire et une équipe réactive pour des réservations simples, rapides et rassurantes.</p>
                <div class="hero-actions">
                    <a href="vehicules.php" class="btn btn-gold"><?= icon('chat', 16) ?> Réserver maintenant</a>
                    <a href="tel:+212600000000" class="btn btn-outline"><?= icon('phone', 16) ?> Appeler</a>
                </div>

                <div class="hero-features">
                    <div class="hero-feature"><div class="ic"><?= icon('car') ?></div><span>Véhicules premium</span></div>
                    <div class="hero-feature"><div class="ic"><?= icon('refresh') ?></div><span>Contrats flexibles</span></div>
                    <div class="hero-feature"><div class="ic"><?= icon('truck') ?></div><span>Livraison à domicile</span></div>
                    <div class="hero-feature"><div class="ic"><?= icon('headset') ?></div><span>Support dédié</span></div>
                </div>
            </div>

            <div class="offer-card">
                <p class="eyebrow">Offre longue durée</p>
                <h3>À partir de 4.500 MAD / mois</h3>
                <p class="desc">Une formule pensée pour les professionnels et les séjours prolongés : disponibilité, coût prévisible et service fiable au quotidien.</p>
                <ul class="offer-list">
                    <li><span class="ic"><?= icon('car', 16) ?></span> Véhicules premium</li>
                    <li><span class="ic"><?= icon('refresh', 16) ?></span> Contrats flexibles</li>
                    <li><span class="ic"><?= icon('truck', 16) ?></span> Livraison à domicile</li>
                    <li><span class="ic"><?= icon('headset', 16) ?></span> Support dédié</li>
                </ul>
                <div class="offer-actions">
                    <a href="contact.php" class="btn btn-dark btn-sm">Demander un devis</a>
                    <a href="tel:+212600000000" class="btn btn-outline btn-sm">Appeler</a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="search-bar">
        <form id="whatsappSearchForm" action="vehicules.php" method="GET">
            <div class="field">
                <label for="lieu"><?= icon('pin', 12) ?> Lieu</label>
                <select name="lieu" id="lieu">
                    <option value="">Sélectionnez un lieu</option>
                    <?php foreach ($villes as $ville): ?>
                        <option value="<?= htmlspecialchars($ville) ?>"><?= htmlspecialchars($ville) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="vehicule_souhaite"><?= icon('car', 12) ?> Véhicule</label>
                <select name="vehicule_souhaite" id="vehicule_souhaite">
                    <option value="">Tous les véhicules   test</option>
                    <?php foreach ($tous_vehicules as $tv): ?>
                        <option value="<?= $tv['id'] ?>"><?= htmlspecialchars($tv['marque'].' '.$tv['modele']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="date_debut"><?= icon('calendar', 12) ?> Départ</label>
                <input type="date" name="date_debut" id="date_debut">
            </div>
            <div class="field">
                <label for="date_fin"><?= icon('calendar', 12) ?> Retour</label>
                <input type="date" name="date_fin" id="date_fin">
            </div>
            <button type="submit" id="whatsappSearchBtn" class="btn btn-gold"><?= icon('chat', 16) ?> Réserver sur WhatsApp</button>
        </form>
    </div>
</div>

<script>
document.getElementById('whatsappSearchForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    var lieu = document.getElementById('lieu').value || 'non précisé';
    var vehSelect = document.getElementById('vehicule_souhaite');
    var vehicule = vehSelect.options[vehSelect.selectedIndex].text;
    var debut = document.getElementById('date_debut').value || 'non précisée';
    var fin = document.getElementById('date_fin').value || 'non précisée';
    var texte = "Bonjour Marivo Car, je souhaite réserver un véhicule.%0ALieu de prise en charge : " + encodeURIComponent(lieu) +
                "%0AVéhicule souhaité : " + encodeURIComponent(vehicule) +
                "%0ADate de départ : " + encodeURIComponent(debut) +
                "%0ADate de retour : " + encodeURIComponent(fin);
    window.open('https://wa.me/212600000000?text=' + texte, '_blank');
});
</script>

<section class="section">
    <div class="container">
        <div class="section-head">
            <p class="eyebrow">Notre flotte</p>
            <h2>Véhicules disponibles</h2>
            <div class="speed-divider"><span class="dot"></span></div>
        </div>

        <?php if ($vehicules): ?>
        <div class="cars-grid">
            <?php foreach ($vehicules as $i => $v): ?>
            <div class="car-card reveal" style="transition-delay: <?= ($i % 3) * 0.1 ?>s;">
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
            <p>Aucun véhicule disponible pour le moment.</p>
        <?php endif; ?>

        <div style="text-align:center; margin-top:36px;">
            <a href="vehicules.php" class="btn btn-outline" style="border-color:var(--orange-dark); color:var(--text-dark);">Voir toute la flotte</a>
        </div>
    </div>
</section>

<section class="section section-dark">
    <div class="container">
        <div class="section-head">
            <p class="eyebrow">Pourquoi Marivo Car</p>
            <h2>Une location simple et sans surprise</h2>
            <div class="speed-divider"><span class="dot"></span></div>
        </div>
        <div class="perks-grid">
            <div class="perk-item reveal" style="transition-delay: 0s;">
                <div class="perk-icon"><?= icon('check', 22) ?></div>
                <h4>Réservation en ligne</h4>
                <p>Choisissez votre véhicule et réservez en quelques clics, 24h/24.</p>
            </div>
            <div class="perk-item reveal" style="transition-delay: 0.1s;">
                <div class="perk-icon"><?= icon('shield', 22) ?></div>
                <h4>Véhicules assurés</h4>
                <p>Toute notre flotte est entretenue régulièrement et couverte par une assurance complète.</p>
            </div>
            <div class="perk-item reveal" style="transition-delay: 0.2s;">
                <div class="perk-icon"><?= icon('card', 22) ?></div>
                <h4>Tarifs transparents</h4>
                <p>Aucun frais caché. Le prix affiché est le prix que vous payez.</p>
            </div>
            <div class="perk-item reveal" style="transition-delay: 0.3s;">
                <div class="perk-icon"><?= icon('pin', 22) ?></div>
                <h4>Plusieurs villes</h4>
                <p>Prise en charge et retour possibles dans plusieurs villes du Maroc.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <p class="eyebrow">Témoignages</p>
            <h2>Ce que disent nos clients</h2>
            <div class="speed-divider"><span class="dot"></span></div>
        </div>

        <?php if ($avis): ?>
        <div class="reviews-grid" style="margin-bottom:36px;">
            <?php foreach ($avis as $i => $a): ?>
            <div class="review-card reveal" style="transition-delay: <?= $i * 0.1 ?>s;">
                <div class="review-stars">
                    <?php for ($j = 0; $j < 5; $j++): ?>
                        <span style="opacity:<?= $j < $a['note'] ? '1' : '0.25' ?>;"><?= icon('star', 16) ?></span>
                    <?php endfor; ?>
                </div>
                <p class="review-quote">"<?= htmlspecialchars($a['texte']) ?>"</p>
                <div class="review-author">
                    <div class="review-avatar"><?= strtoupper(substr($a['nom'], 0, 1)) ?></div>
                    <div>
                        <div class="name"><?= htmlspecialchars($a['nom']) ?></div>
                        <div class="meta"><?= htmlspecialchars($a['ville'] ?: '') ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p style="color:var(--text-muted); margin-bottom:36px;">Aucun avis pour le moment. Soyez le premier à en laisser un !</p>
        <?php endif; ?>

        <div class="avis-form-card">
            <div class="avis-form-header">
                <div class="ic"><?= icon('chat', 22) ?></div>
                <div>
                    <h3>Laisser un avis</h3>
                    <p>Votre avis sera publié après vérification par notre équipe.</p>
                </div>
            </div>

            <?php if ($avis_succes): ?>
                <div class="alert alert-success">Merci pour votre avis ! Il sera publié après validation.</div>
            <?php endif; ?>
            <?php if ($avis_erreurs): ?>
                <div class="alert alert-error"><ul style="padding-left:18px;"><?php foreach ($avis_erreurs as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="icon-label"><?= icon('users', 14) ?> Nom *</label>
                        <input type="text" name="avis_nom" placeholder="Votre nom" required>
                    </div>
                    <div class="form-group">
                        <label class="icon-label"><?= icon('pin', 14) ?> Ville</label>
                        <input type="text" name="avis_ville" placeholder="Votre ville">
                    </div>
                </div>

               <div class="form-group">
    <label class="icon-label"><?= icon('star', 14) ?> Votre note</label>
    <div class="rating-picker">
        <input type="radio" id="star1" name="avis_note" value="1"><label for="star1"><span class="emoji">😞</span><span class="txt">Décevant</span></label>
        <input type="radio" id="star2" name="avis_note" value="2"><label for="star2"><span class="emoji">😐</span><span class="txt">Moyen</span></label>
        <input type="radio" id="star3" name="avis_note" value="3"><label for="star3"><span class="emoji">🙂</span><span class="txt">Bien</span></label>
        <input type="radio" id="star4" name="avis_note" value="4"><label for="star4"><span class="emoji">😊</span><span class="txt">Très bien</span></label>
        <input type="radio" id="star5" name="avis_note" value="5" checked><label for="star5"><span class="emoji">🤩</span><span class="txt">Excellent</span></label>
    </div>
</div>

                <div class="form-group">
                    <label class="icon-label"><?= icon('mail', 14) ?> Votre avis *</label>
                    <textarea name="avis_texte" placeholder="Partagez votre expérience avec Marivo Car..." required></textarea>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;"><?= icon('check', 16) ?> Envoyer mon avis</button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>