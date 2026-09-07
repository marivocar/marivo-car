<?php
require_once __DIR__ . '/includes/db.php';

$page_title = 'Contact';
$active = 'contact';

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($nom === '') $erreurs[] = "Le nom est requis.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "L'adresse email n'est pas valide.";
    if ($message === '') $erreurs[] = "Le message ne peut pas être vide.";

    if (empty($erreurs)) {
        $insert = $pdo->prepare("INSERT INTO messages (nom, email, sujet, message) VALUES (:nom, :email, :sujet, :message)");
        $insert->execute([':nom' => $nom, ':email' => $email, ':sujet' => $sujet, ':message' => $message]);
        $succes = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:46px;">
    <div class="container">
        <div class="section-head">
            <p class="eyebrow">Contact</p>
            <h2>Une question ? Écrivez-nous</h2>
            <p style="color:var(--text-muted); max-width:520px; margin-top:10px;">
                Notre équipe est disponible pour répondre à toutes vos questions concernant nos véhicules,
                les réservations ou toute autre demande.
            </p>
            <div class="speed-divider"><span class="dot"></span></div>
        </div>

        <div class="detail-grid">
            <div>
                <?php if ($succes): ?>
                    <div class="alert alert-success">Votre message a bien été envoyé. Nous vous répondrons rapidement.</div>
                <?php endif; ?>
                <?php if ($erreurs): ?>
                    <div class="alert alert-error">
                        <ul style="padding-left:18px;">
                            <?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="form-card">
                    <h3 style="font-size:1.1rem; margin-bottom:22px;">Envoyez-nous un message</h3>
                    <form action="contact.php" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom complet *</label>
                                <input type="text" name="nom" id="nom" placeholder="Votre nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" name="email" id="email" placeholder="vous@exemple.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sujet">Sujet</label>
                            <input type="text" name="sujet" id="sujet" placeholder="Ex : Question sur une réservation" value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea name="message" id="message" placeholder="Écrivez votre message ici..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-gold"><?= icon('chat', 16) ?> Envoyer le message</button>
                    </form>
                </div>
            </div>

            <div class="contact-card-dark">
                <p class="eyebrow">Coordonnées</p>
                <h3>Restons en contact</h3>

                <ul class="contact-info-list">
                    <li>
                        <span class="ic"><?= icon('pin', 18) ?></span>
                        <div>
                            <span class="info-label">Adresse</span>
                            <span class="info-value">123 Avenue Mohammed V, Marrakech</span>
                        </div>
                    </li>
                    <li>
                        <span class="ic"><?= icon('phone', 18) ?></span>
                        <div>
                            <span class="info-label">Téléphone</span>
                            <span class="info-value">+212 6 00 00 00 00</span>
                        </div>
                    </li>
                    <li>
                        <span class="ic"><?= icon('mail', 18) ?></span>
                        <div>
                            <span class="info-label">Email</span>
                            <span class="info-value">contact@marivocar.ma</span>
                        </div>
                    </li>
                    <li>
                        <span class="ic"><?= icon('clock', 18) ?></span>
                        <div>
                            <span class="info-label">Horaires</span>
                            <span class="info-value">Lun - Sam : 8h00 - 20h00</span>
                        </div>
                    </li>
                </ul>

                <div class="response-note">
                    <?= icon('check', 16) ?>
                    Réponse sous 24h ouvrées
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>