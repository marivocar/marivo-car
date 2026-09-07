<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Marivo Car">
                <p>Marivo Car vous accompagne dans tous vos déplacements avec une flotte moderne, entretenue et disponible partout au Maroc.</p>
            </div>
            <div>
                <h4>Navigation</h4>
                <ul>
                    <li><a href="<?= SITE_URL ?>/index.php">Accueil</a></li>
                    <li><a href="<?= SITE_URL ?>/vehicules.php">Nos véhicules</a></li>
                    <li><a href="<?= SITE_URL ?>/a-propos.php">À propos</a></li>
                    <li><a href="<?= SITE_URL ?>/contact.php">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Catégories</h4>
                <ul>
                    <li><a href="<?= SITE_URL ?>/vehicules.php?categorie=2">Citadines</a></li>
                    <li><a href="<?= SITE_URL ?>/vehicules.php?categorie=4">SUV</a></li>
                    <li><a href="<?= SITE_URL ?>/vehicules.php?categorie=3">Berlines</a></li>
                    <li><a href="<?= SITE_URL ?>/vehicules.php?categorie=5">Luxe</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    <li>123 Avenue Mohammed V, Marrakech</li>
                    <li>+212 6 00 00 00 00</li>
                    <li>contact@marivocar.ma</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> Marivo Car — Tous droits réservés.
        </div>
    </div>
</footer>

<script>
document.getElementById('navToggle')?.addEventListener('click', function() {
    document.getElementById('mainNav').classList.toggle('open');
});

// --- Animation scroll reveal ---
(function () {
    var elements = document.querySelectorAll('.reveal');
    if (!elements.length) return;

    if (!('IntersectionObserver' in window)) {
        elements.forEach(function (el) { el.classList.add('is-visible'); });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    elements.forEach(function (el) { observer.observe(el); });
})();
</script>
</body>
</html>