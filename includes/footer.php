<?php
declare(strict_types=1);

$err = ip_flash_get('error');
$ok = ip_flash_get('success');
$u = static fn (string $p): string => ip_url($p);
?>
<?php if ($err): ?>
    <div class="ip-toast ip-toast--error ft-animate-in" role="alert"><?= ip_h($err) ?></div>
<?php endif; ?>
<?php if ($ok): ?>
    <div class="ip-toast ip-toast--ok ft-animate-in" role="status"><?= ip_h($ok) ?></div>
<?php endif; ?>

<div id="app-footer">
    <footer class="app-footer">
        <div class="app-footer__brand">
            <img src="<?= ip_h(ip_url('assets/images/favicon.svg')) ?>" alt="" class="app-footer__logo" width="36" height="36">
            <div>
                <strong class="app-footer__name">IronPulse</strong>
                <p class="app-footer__tagline">Train smarter. Track everything.</p>
                <div class="app-footer__social" aria-label="Social">
                    <a href="#" aria-label="Instagram (demo)"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                    <a href="#" aria-label="YouTube (demo)"><i class="fab fa-youtube" aria-hidden="true"></i></a>
                    <a href="#" aria-label="Strava (demo)"><i class="fab fa-strava" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
        <div class="app-footer__product app-footer__col">
            <h4>Product</h4>
            <a href="<?= ip_h($u('pages/dashboard.php')) ?>">Dashboard</a>
            <a href="<?= ip_h($u('pages/workouts.php')) ?>">Workouts</a>
            <a href="<?= ip_h($u('pages/routines.php')) ?>">Routines</a>
            <a href="<?= ip_h($u('pages/exercises.php')) ?>">Exercise library</a>
        </div>
        <div class="app-footer__account app-footer__col">
            <h4>Account</h4>
            <a href="<?= ip_h($u('pages/profile.php')) ?>">Profile</a>
            <a href="<?= ip_h($u('pages/settings.php')) ?>">Settings</a>
            <a href="<?= ip_h($u('pages/coaches.php')) ?>">Coaches</a>
            <a href="<?= ip_h($u('pages/meals.php')) ?>">Meal plans</a>
        </div>
        <p class="app-footer__copy">© <?= (int) date('Y') ?> IronPulse. Built for consistent training.</p>
    </footer>
</div>
</div>
</div>
<script defer src="<?= ip_h(ip_url('assets/js/ip-app.js')) ?>"></script>
<?php require __DIR__ . '/scripts.php'; ?>
