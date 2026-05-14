<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
ip_require_login();

$currentUser = ip_current_user($pdo);
if (!$currentUser) {
    session_destroy();
    ip_redirect('pages/login.php');
}

$pageTitle = 'Settings';
$currentNav = 'settings';

require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/sidebar.php';
require_once dirname(__DIR__) . '/includes/navbar.php';
?>
<main class="app-main">
    <section class="dash-hero">
        <div class="dash-hero-inner">
            <span class="tag">ACCOUNT</span>
            <h1>Settings</h1>
            <p>Preferences and account controls use the same layout as the rest of the app.</p>
        </div>
    </section>
    <div class="ft-surface" style="max-width:720px;border-radius:var(--radius-xl);border:1px solid var(--border);padding:var(--space-6)">
        <h2 style="margin-top:0;font-size:1.1rem">Session</h2>
        <p class="ft-muted" style="margin-bottom:var(--space-4)">You are signed in as <strong><?= ip_h((string) ($currentUser['email'] ?? '')) ?></strong>.</p>
        <p class="ft-muted" style="margin-bottom:var(--space-4)">Edit profile details on the profile page.</p>
        <a class="ft-btn ft-btn--secondary" href="<?= ip_h(ip_url('pages/profile.php')) ?>">Open profile</a>
    </div>
</main>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
