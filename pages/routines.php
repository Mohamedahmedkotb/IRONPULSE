<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';
ip_require_login();

$currentUser = ip_current_user($pdo);
if (!$currentUser) {
    session_destroy();
    ip_redirect('pages/login.php');
}

$userId = (int) $_SESSION['user_id'];
$st = $pdo->prepare(
    'SELECT r.*, (SELECT COUNT(*) FROM routine_exercises re WHERE re.routine_id = r.id) AS exercise_count
     FROM routines r
     WHERE r.user_id = ?
     ORDER BY r.id DESC',
);
$st->execute([$userId]);
$routines = $st->fetchAll();

$pageTitle = 'Routines';
$currentNav = 'routines';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<main class="app-main">
    <h1 style="margin-top:0">Routines</h1>
    <p class="ft-muted">Programs linked to your account (from MySQL).</p>
    <div class="dash-programs" style="margin-top:var(--space-5)">
        <?php foreach ($routines as $r): ?>
            <article class="program-tile">
                <div class="bg" style="background-image:url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=70')"></div>
                <div class="scrim"></div>
                <div class="body">
                    <h3><?= ip_h((string) $r['title']) ?></h3>
                    <p style="margin:0;font-size:0.85rem;opacity:0.9"><?= (int) $r['exercise_count'] ?> exercises</p>
                    <p style="margin:0.35rem 0 0;font-size:0.8rem;opacity:0.85"><?= ip_h(mb_substr((string) ($r['description'] ?? ''), 0, 120)) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (!$routines): ?>
            <p class="ft-muted">No routines yet. Seed data or add rows in <code>routines</code> / <code>routine_exercises</code>.</p>
        <?php endif; ?>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
