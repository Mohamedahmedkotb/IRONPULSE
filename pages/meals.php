<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
ip_require_login();

$currentUser = ip_current_user($pdo);
if (!$currentUser) {
    session_destroy();
    ip_redirect('pages/login.php');
}

$userId = (int) $_SESSION['user_id'];
$st = $pdo->prepare(
    'SELECT plan_date, title, breakfast, lunch, dinner, calories, protein_g, carbs_g, fats_g FROM meal_plans WHERE user_id = ? ORDER BY plan_date DESC LIMIT 30',
);
$st->execute([$userId]);
$meals = $st->fetchAll();

$pageTitle = 'Meal plans';
$currentNav = 'meals';

require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/sidebar.php';
require_once dirname(__DIR__) . '/includes/navbar.php';
?>
<main class="app-main">
    <h1 style="margin-top:0">Meal plans</h1>
    <p class="ft-muted">Rows from <code>meal_plans</code> for your user.</p>
    <div style="margin-top:var(--space-5);display:flex;flex-direction:column;gap:var(--space-3)">
        <?php foreach ($meals as $m): ?>
            <article class="ft-surface" style="border-radius:var(--radius-lg);border:1px solid var(--border);padding:var(--space-4)">
                <div class="ft-flex-between" style="align-items:flex-start">
                    <strong><?= ip_h((string) $m['plan_date']) ?> — <?= ip_h((string) $m['title']) ?></strong>
                    <span class="ft-pill is-active"><?= (int) $m['calories'] ?> kcal</span>
                </div>
                <p style="margin:0.5rem 0 0;font-size:0.9rem"><strong>B:</strong> <?= ip_h((string) $m['breakfast']) ?></p>
                <p style="margin:0.25rem 0 0;font-size:0.9rem"><strong>L:</strong> <?= ip_h((string) $m['lunch']) ?></p>
                <p style="margin:0.25rem 0 0;font-size:0.9rem"><strong>D:</strong> <?= ip_h((string) $m['dinner']) ?></p>
                <p class="ft-muted" style="margin:0.35rem 0 0;font-size:0.8rem">P <?= (int) $m['protein_g'] ?>g · C <?= (int) $m['carbs_g'] ?>g · F <?= (int) $m['fats_g'] ?>g</p>
            </article>
        <?php endforeach; ?>
        <?php if (!$meals): ?>
            <p class="ft-muted">No meal plans yet.</p>
        <?php endif; ?>
    </div>
</main>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
