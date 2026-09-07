<?php
require_once __DIR__ . '/includes/db.php';

$page_title = 'À propos';
$active = 'apropos';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:46px;">
    <div class="container" style="max-width:820px;">
        <p class="eyebrow">À propos de nous</p>
        <h2 style="margin-bottom:8px;">Marivo Car, votre partenaire mobilité</h2>
        <div class="speed-divider"><span class="dot"></span></div>

        <p style="color:var(--text-muted); margin-bottom:20px;">
            Marivo Car est une société marocaine de location de voitures fondée pour rendre la mobilité
            simple, fiable et accessible. Depuis nos débuts, nous mettons un point d'honneur à proposer
            une flotte de véhicules récents et bien entretenus, ainsi qu'un service client réactif.
        </p>
        <p style="color:var(--text-muted); margin-bottom:40px;">
            Que ce soit pour un déplacement professionnel, des vacances en famille ou un besoin ponctuel,
            notre équipe vous accompagne à chaque étape, de la réservation en ligne jusqu'à la restitution
            du véhicule.
        </p>

        <div class="perks-grid">
            <div class="perk-item">
                <div class="perk-icon"><?= icon('car') ?></div>
                <h4>Flotte variée</h4>
                <p>De la citadine économique au SUV premium, un véhicule pour chaque besoin.</p>
            </div>
            <div class="perk-item">
                <div class="perk-icon"><?= icon('users') ?></div>
                <h4>Service personnalisé</h4>
                <p>Une équipe disponible pour répondre à toutes vos questions.</p>
            </div>
            <div class="perk-item">
                <div class="perk-icon"><?= icon('shield') ?></div>
                <h4>Qualité garantie</h4>
                <p>Véhicules inspectés et entretenus avant chaque location.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>