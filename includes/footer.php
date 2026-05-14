<?php
$err = ip_flash_get('error');
$ok = ip_flash_get('success');
?>
<?php if ($err): ?>
    <div class="ip-toast ip-toast--error ft-animate-in" role="alert" style="position:fixed;top:1.25rem;right:1.25rem;z-index:9999;padding:0.85rem 1.2rem;border-radius:var(--radius-md);background:#fef2f2;border:1px solid #fecaca;color:#991b1b;max-width:320px;box-shadow:var(--shadow-lg);"><?= ip_h($err) ?></div>
<?php endif; ?>
<?php if ($ok): ?>
    <div class="ip-toast ip-toast--ok ft-animate-in" role="status" style="position:fixed;top:1.25rem;right:1.25rem;z-index:9999;padding:0.85rem 1.2rem;border-radius:var(--radius-md);background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;max-width:320px;box-shadow:var(--shadow-lg);"><?= ip_h($ok) ?></div>
<?php endif; ?>

<div id="app-footer">
    <footer class="app-footer">
        <div class="app-footer__brand">
            <img src="<?= ip_h(ip_url('assets/images/favicon.svg')) ?>" alt="" class="app-footer__logo" width="36" height="36">
            <div>
                <strong class="app-footer__name">IronPulse</strong>
                <p class="app-footer__tagline">Train smarter. Track everything.</p>
            </div>
        </div>
        <nav class="app-footer__nav" aria-label="Footer">
            <a href="<?= ip_h(ip_url('pages/dashboard.php')) ?>">Dashboard</a>
            <a href="<?= ip_h(ip_url('pages/profile.php')) ?>">Profile</a>
        </nav>
        <p class="app-footer__copy">© <?= (int) date('Y') ?> IronPulse. PHP template mode.</p>
    </footer>
</div>
</div>
</div>
<?php require __DIR__ . '/scripts.php'; ?>
