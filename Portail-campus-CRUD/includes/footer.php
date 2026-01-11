    </div><!-- /.container -->

    <footer class="bg-light py-4 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-1">
                        <i class="bi bi-mortarboard-fill text-primary"></i>
                        <strong>Portail Campus</strong> - Système de Gestion des Étudiants
                    </p>
                    <small class="text-muted">
                        &copy; <?= date('Y') ?> Licence 3 GLAR - Université du Sénégal
                    </small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small class="text-muted d-block mb-2">
                        <i class="bi bi-code-slash"></i> Développé avec PHP, MySQL & Docker
                    </small>
                    <?php if (isLoggedIn()): ?>
                    <span class="badge bg-success">
                        <i class="bi bi-shield-check"></i> Session active
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.flash-message').forEach(function(el) {
                el.style.transition = 'opacity 0.5s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);

        // Initialize Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Session timeout warning (5 minutes before expiration)
        <?php if (isLoggedIn()): ?>
        (function() {
            const timeUntilTimeout = <?= getTimeUntilTimeout() ?> * 1000;
            const warningTime = Math.max(0, timeUntilTimeout - 300000); // 5 min avant
            
            if (warningTime > 0) {
                setTimeout(function() {
                    if (confirm('⚠️ Votre session expire dans 5 minutes.\n\nVoulez-vous prolonger votre session ?')) {
                        // Refresh page to extend session
                        location.reload();
                    }
                }, warningTime);
            }
        })();
        <?php endif; ?>
    </script>
</body>
</html>
