<?php
require_once __DIR__ . '/includes/db.php';

$page_title = 'Réservation';
$active = 'vehicules';

$erreurs = [];
$succes = false;

// Déterminer le véhicule concerné (via GET au premier chargement, via POST hidden field ensuite)
$vehicule_id = (int)($_POST['vehicule_id'] ?? $_GET['vehicule_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = :id AND disponible = 1");
$stmt->execute([':id' => $vehicule_id]);
$vehicule = $stmt->fetch();

if (!$vehicule) {
    header('Location: vehicules.php');
    exit;
}

// --- Traitement du formulaire ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = trim($_POST['nom_client'] ?? '');
    $email     = trim($_POST['email_client'] ?? '');
    $indicatif = trim($_POST['indicatif'] ?? '+212');
    $telephone_brut = trim($_POST['telephone_client'] ?? '');
    $telephone = $telephone_brut !== '' ? $indicatif . ' ' . $telephone_brut : '';    $lieu      = trim($_POST['lieu_prise_charge'] ?? '');
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin   = $_POST['date_fin'] ?? '';

    if ($nom === '') $erreurs[] = "Le nom complet est requis.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "L'adresse email n'est pas valide.";
    if ($telephone === '') $erreurs[] = "Le numéro de téléphone est requis.";
    if ($lieu === '') $erreurs[] = "Le lieu de prise en charge est requis.";

    $debut = DateTime::createFromFormat('Y-m-d', $date_debut);
    $fin   = DateTime::createFromFormat('Y-m-d', $date_fin);

    if (!$debut || !$fin) {
        $erreurs[] = "Merci de renseigner des dates valides.";
    } elseif ($fin <= $debut) {
        $erreurs[] = "La date de fin doit être après la date de début.";
    } elseif ($debut < new DateTime('today')) {
        $erreurs[] = "La date de début ne peut pas être dans le passé.";
    }

    // Vérifier qu'il n'y a pas déjà une réservation confirmée qui chevauche ces dates
    if (empty($erreurs)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM reservations
                                 WHERE vehicule_id = :vid
                                 AND statut IN ('En attente','Confirmée')
                                 AND date_debut < :fin AND date_fin > :debut");
        $check->execute([
            ':vid' => $vehicule_id,
            ':debut' => $date_debut,
            ':fin' => $date_fin,
        ]);
        if ($check->fetchColumn() > 0) {
            $erreurs[] = "Ce véhicule est déjà réservé sur une partie de cette période. Merci de choisir d'autres dates.";
        }
    }

    if (empty($erreurs)) {
        $nb_jours = max(1, $debut->diff($fin)->days);
        $prix_total = $nb_jours * $vehicule['prix_jour'];

        $insert = $pdo->prepare("INSERT INTO reservations
            (vehicule_id, nom_client, email_client, telephone_client, date_debut, date_fin, lieu_prise_charge, prix_total)
            VALUES (:vid, :nom, :email, :tel, :debut, :fin, :lieu, :prix)");
        $insert->execute([
            ':vid' => $vehicule_id,
            ':nom' => $nom,
            ':email' => $email,
            ':tel' => $telephone,
            ':debut' => $date_debut,
            ':fin' => $date_fin,
            ':lieu' => $lieu,
            ':prix' => $prix_total,
        ]);

        $succes = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:40px;">
    <div class="container" style="max-width:1040px;">
        <a href="vehicule-detail.php?id=<?= $vehicule['id'] ?>" class="back-link">&larr; Retour au véhicule</a>

        <p class="eyebrow">Réservation</p>
        <h2 style="margin-bottom:8px;">Finalisez votre demande</h2>
        <p style="color:var(--text-muted); margin-bottom:30px;">Remplissez le formulaire ci-dessous, notre équipe confirmera votre réservation rapidement.</p>

        <?php if ($succes): ?>
            <div class="alert alert-success">
                <strong>Votre demande de réservation a bien été envoyée !</strong><br>
                Notre équipe vous contactera très prochainement pour confirmer votre réservation.
            </div>
            <a href="vehicules.php" class="btn btn-outline" style="border-color:var(--orange-dark); color:var(--text-dark);">Voir d'autres véhicules</a>
        <?php else: ?>

            <div class="reservation-layout">
                <div>
                    <?php if ($erreurs): ?>
                        <div class="alert alert-error">
                            <ul style="padding-left:18px;">
                                <?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="form-card">
                        <h3 style="font-size:1.05rem; margin-bottom:20px;">Vos coordonnées</h3>
                        <form action="reservation.php" method="POST">
                            <input type="hidden" name="vehicule_id" value="<?= $vehicule['id'] ?>">

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nom_client"><?= icon('users', 14) ?> Nom complet *</label>
                                    <input type="text" name="nom_client" id="nom_client" placeholder="Votre nom" value="<?= htmlspecialchars($_POST['nom_client'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
    <label for="telephone_client"><?= icon('phone', 14) ?> Téléphone *</label>
    <div class="phone-input-group">
        <div class="country-select" id="countrySelect">
            <button type="button" class="country-select-btn" id="countryBtn">
                <span class="flag" id="selectedFlag">🇲🇦</span>
                <span id="selectedCode">+212</span>
                <span class="chevron"><?= icon('chevron', 14) ?></span>
            </button>
            <div class="country-dropdown" id="countryDropdown">
                <div class="country-search-wrap">
                    <input type="text" class="country-search" id="countrySearch" placeholder="Rechercher un pays...">
                </div>
                <div class="country-list" id="countryList"></div>
            </div>
        </div>
        <input type="hidden" name="indicatif" id="indicatifInput" value="+212">
        <input type="tel" name="telephone_client" id="telephone_client" placeholder="6 00 00 00 00" value="<?= htmlspecialchars($_POST['telephone_client'] ?? '') ?>" required>
    </div>
</div>

<script>
(function () {
    var pays = [
        ["🇲🇦","Maroc","+212"],["🇫🇷","France","+33"],["🇪🇸","Espagne","+34"],["🇩🇿","Algérie","+213"],
        ["🇹🇳","Tunisie","+216"],["🇧🇪","Belgique","+32"],["🇨🇭","Suisse","+41"],["🇩🇪","Allemagne","+49"],
        ["🇮🇹","Italie","+39"],["🇳🇱","Pays-Bas","+31"],["🇬🇧","Royaume-Uni","+44"],["🇺🇸","États-Unis","+1"],
        ["🇨🇦","Canada","+1"],["🇵🇹","Portugal","+351"],["🇸🇪","Suède","+46"],["🇳🇴","Norvège","+47"],
        ["🇩🇰","Danemark","+45"],["🇫🇮","Finlande","+358"],["🇮🇪","Irlande","+353"],["🇦🇹","Autriche","+43"],
        ["🇵🇱","Pologne","+48"],["🇬🇷","Grèce","+30"],["🇷🇴","Roumanie","+40"],["🇭🇺","Hongrie","+36"],
        ["🇨🇿","Tchéquie","+420"],["🇸🇰","Slovaquie","+421"],["🇧🇬","Bulgarie","+359"],["🇭🇷","Croatie","+385"],
        ["🇺🇦","Ukraine","+380"],["🇷🇺","Russie","+7"],["🇹🇷","Turquie","+90"],["🇦🇪","Émirats arabes unis","+971"],
        ["🇸🇦","Arabie saoudite","+966"],["🇶🇦","Qatar","+974"],["🇰🇼","Koweït","+965"],["🇧🇭","Bahreïn","+973"],
        ["🇴🇲","Oman","+968"],["🇯🇴","Jordanie","+962"],["🇱🇧","Liban","+961"],["🇮🇶","Irak","+964"],
        ["🇮🇱","Israël","+972"],["🇵🇸","Palestine","+970"],["🇪🇬","Égypte","+20"],["🇱🇾","Libye","+218"],
        ["🇲🇷","Mauritanie","+222"],["🇸🇳","Sénégal","+221"],["🇨🇮","Côte d'Ivoire","+225"],["🇲🇱","Mali","+223"],
        ["🇧🇫","Burkina Faso","+226"],["🇳🇪","Niger","+227"],["🇹🇬","Togo","+228"],["🇧🇯","Bénin","+229"],
        ["🇬🇭","Ghana","+233"],["🇳🇬","Nigeria","+234"],["🇨🇲","Cameroun","+237"],["🇬🇦","Gabon","+241"],
        ["🇨🇬","Congo","+242"],["🇨🇩","RD Congo","+243"],["🇷🇼","Rwanda","+250"],["🇰🇪","Kenya","+254"],
        ["🇹🇿","Tanzanie","+255"],["🇺🇬","Ouganda","+256"],["🇪🇹","Éthiopie","+251"],["🇿🇦","Afrique du Sud","+27"],
        ["🇲🇬","Madagascar","+261"],["🇩🇯","Djibouti","+253"],["🇹🇩","Tchad","+235"],["🇸🇩","Soudan","+249"],
        ["🇨🇳","Chine","+86"],["🇯🇵","Japon","+81"],["🇰🇷","Corée du Sud","+82"],["🇮🇳","Inde","+91"],
        ["🇵🇰","Pakistan","+92"],["🇧🇩","Bangladesh","+880"],["🇮🇩","Indonésie","+62"],["🇲🇾","Malaisie","+60"],
        ["🇸🇬","Singapour","+65"],["🇹🇭","Thaïlande","+66"],["🇻🇳","Vietnam","+84"],["🇵🇭","Philippines","+63"],
        ["🇭🇰","Hong Kong","+852"],["🇹🇼","Taïwan","+886"],["🇦🇫","Afghanistan","+93"],["🇮🇷","Iran","+98"],
        ["🇦🇺","Australie","+61"],["🇳🇿","Nouvelle-Zélande","+64"],["🇧🇷","Brésil","+55"],["🇦🇷","Argentine","+54"],
        ["🇲🇽","Mexique","+52"],["🇨🇱","Chili","+56"],["🇨🇴","Colombie","+57"],["🇵🇪","Pérou","+51"],
        ["🇻🇪","Venezuela","+58"],["🇪🇨","Équateur","+593"],["🇺🇾","Uruguay","+598"],["🇧🇴","Bolivie","+591"],
        ["🇵🇾","Paraguay","+595"],["🇨🇺","Cuba","+53"],["🇩🇴","Rép. dominicaine","+1"],["🇬🇹","Guatemala","+502"]
    ];

    var btn = document.getElementById('countryBtn');
    var wrap = document.getElementById('countrySelect');
    var dropdown = document.getElementById('countryDropdown');
    var search = document.getElementById('countrySearch');
    var list = document.getElementById('countryList');
    var flagEl = document.getElementById('selectedFlag');
    var codeEl = document.getElementById('selectedCode');
    var hiddenInput = document.getElementById('indicatifInput');

    function renderList(filtre) {
        filtre = (filtre || '').toLowerCase();
        list.innerHTML = '';
        var trouve = 0;
        pays.forEach(function (p) {
            if (filtre && p[1].toLowerCase().indexOf(filtre) === -1 && p[2].indexOf(filtre) === -1) return;
            trouve++;
            var opt = document.createElement('div');
            opt.className = 'country-option';
            opt.innerHTML = '<span class="flag">' + p[0] + '</span><span class="name">' + p[1] + '</span><span class="code">' + p[2] + '</span>';
            opt.addEventListener('click', function () {
                flagEl.textContent = p[0];
                codeEl.textContent = p[2];
                hiddenInput.value = p[2];
                wrap.classList.remove('open');
            });
            list.appendChild(opt);
        });
        if (trouve === 0) {
            list.innerHTML = '<div class="country-empty">Aucun pays trouvé</div>';
        }
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        wrap.classList.toggle('open');
        if (wrap.classList.contains('open')) {
            search.value = '';
            renderList('');
            setTimeout(function () { search.focus(); }, 50);
        }
    });

    search.addEventListener('input', function () { renderList(search.value); });

    document.addEventListener('click', function (e) {
        if (!wrap.contains(e.target)) wrap.classList.remove('open');
    });

    renderList('');
})();
</script>
                            </div>

                            <div class="form-group">
                                <label for="email_client"><?= icon('mail', 14) ?> Email *</label>
                                <input type="email" name="email_client" id="email_client" placeholder="vous@exemple.com" value="<?= htmlspecialchars($_POST['email_client'] ?? '') ?>" required>
                            </div>

                            <h3 style="font-size:1.05rem; margin:26px 0 20px;">Détails de la location</h3>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="date_debut"><?= icon('calendar', 14) ?> Date de début *</label>
                                    <input type="date" name="date_debut" id="date_debut" value="<?= htmlspecialchars($_POST['date_debut'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="date_fin"><?= icon('calendar', 14) ?> Date de fin *</label>
                                    <input type="date" name="date_fin" id="date_fin" value="<?= htmlspecialchars($_POST['date_fin'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="lieu_prise_charge"><?= icon('pin', 14) ?> Lieu de prise en charge *</label>
                                <input type="text" name="lieu_prise_charge" id="lieu_prise_charge" placeholder="Ex: Aéroport Marrakech-Ménara" value="<?= htmlspecialchars($_POST['lieu_prise_charge'] ?? '') ?>" required>
                            </div>

                            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; margin-top:10px;"><?= icon('check', 16) ?> Confirmer la demande de réservation</button>
                        </form>
                    </div>
                </div>

                <div class="reservation-summary">
                    <img src="assets/uploads/cars/<?= htmlspecialchars($vehicule['image']) ?>" alt="<?= htmlspecialchars($vehicule['marque'].' '.$vehicule['modele']) ?>"
                         onerror="this.src='assets/images/default-car.jpg'">
                    <h4><?= htmlspecialchars($vehicule['marque'].' '.$vehicule['modele']) ?></h4>
                    <p class="price-line"><?= number_format($vehicule['prix_jour'], 0) ?> MAD / jour</p>
                    <ul>
                        <li><span class="ic"><?= icon('shield', 16) ?></span> Assurance incluse</li>
                        <li><span class="ic"><?= icon('check', 16) ?></span> Annulation gratuite 48h avant</li>
                        <li><span class="ic"><?= icon('clock', 16) ?></span> Confirmation sous 24h</li>
                        <li><span class="ic"><?= icon('headset', 16) ?></span> Support 24h/24</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>