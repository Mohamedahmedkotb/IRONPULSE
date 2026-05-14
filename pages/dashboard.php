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

$st = $pdo->prepare('SELECT COUNT(*) FROM workouts WHERE user_id = ?');
$st->execute([$userId]);
$workoutCount = (int) $st->fetchColumn();

$st = $pdo->prepare(
    'SELECT COALESCE(SUM(calories),0) FROM workouts WHERE user_id = ? AND workout_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)',
);
$st->execute([$userId]);
$calWeek = (int) $st->fetchColumn();

$st = $pdo->prepare('SELECT streak FROM users WHERE id = ?');
$st->execute([$userId]);
$streak = (int) $st->fetchColumn();

$st = $pdo->prepare(
    'SELECT id, title, category, duration, calories, workout_date, notes FROM workouts WHERE user_id = ? ORDER BY workout_date DESC, id DESC LIMIT 5',
);
$st->execute([$userId]);
$recent = $st->fetchAll();

$st = $pdo->prepare(
    'SELECT calories FROM workouts WHERE user_id = ? ORDER BY workout_date DESC, id DESC LIMIT 7',
);
$st->execute([$userId]);
$calSeries = array_map('intval', array_reverse($st->fetchAll(PDO::FETCH_COLUMN) ?: []));

$pageTitle = 'Dashboard';
$currentNav = 'home';
$includeChartScript = true;

require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/sidebar.php';
require_once dirname(__DIR__) . '/includes/navbar.php';
?>
<main class="app-main">
    <section class="dash-hero">
        <div class="dash-hero-inner">
            <span class="tag">OVERVIEW</span>
            <h1>Welcome back, <?= ip_h($currentUser['full_name'] ?? 'Athlete') ?>.</h1>
            <p>Your training snapshot updates on every visit—clear metrics, calm layout, zero noise.</p>
        </div>
    </section>

    <section class="dash-stats" aria-label="Summary stats">
        <article class="dash-stat-card ft-animate-in">
            <div class="label"><i class="fas fa-dumbbell"></i> WORKOUTS</div>
            <div class="num" data-counter="<?= (int) $workoutCount ?>"><?= (int) $workoutCount ?></div>
            <p class="sub">Total sessions logged</p>
        </article>
        <article class="dash-stat-card ft-animate-in ft-animate-in-delay-1">
            <div class="label"><i class="fas fa-fire"></i> STREAK</div>
            <div class="num" data-counter="<?= (int) $streak ?>"><?= (int) $streak ?></div>
            <p class="sub">Days (stored on profile)</p>
        </article>
        <article class="dash-stat-card ft-animate-in ft-animate-in-delay-2">
            <div class="label"><i class="fas fa-bolt"></i> 7-DAY KCAL</div>
            <div class="num" data-counter="<?= (int) $calWeek ?>"><?= (int) $calWeek ?></div>
            <p class="sub">Calories logged this week</p>
        </article>
    </section>

    <div class="dash-grid">
        <div class="dash-chart-card ft-surface ft-animate-in" style="border-radius:var(--radius-lg);border:1px solid var(--border);padding:var(--space-5)">
            <div class="ft-flex-between" style="margin-bottom:var(--space-4)">
                <h2 style="margin:0;font-size:1.1rem">Activity (last sessions)</h2>
                <span class="ft-muted" style="font-size:0.85rem">kcal</span>
            </div>
            <canvas id="chart-home-line" width="800" height="220" aria-label="Calories chart"></canvas>
        </div>
        <aside class="ft-surface ft-animate-in ft-animate-in-delay-1" style="border-radius:var(--radius-lg);border:1px solid var(--border);padding:var(--space-5)">
            <h2 style="margin:0 0 var(--space-4);font-size:1.1rem">Recent workouts</h2>
            <ul class="activity-list">
                <?php if (!$recent): ?>
                    <li class="ft-muted">No workouts yet. <a href="<?= ip_h(ip_url('pages/workouts.php')) ?>">Add one</a>.</li>
                <?php else: ?>
                    <?php foreach ($recent as $r): ?>
                        <li>
                            <div class="activity-ico blue"><i class="fas fa-dumbbell"></i></div>
                            <div>
                                <div class="ft-flex-between" style="margin:0;align-items:flex-start">
                                    <strong><?= ip_h((string) $r['title']) ?></strong>
                                    <span class="ft-muted" style="font-size:0.75rem"><?= ip_h((string) $r['workout_date']) ?></span>
                                </div>
                                <p class="ft-muted" style="font-size:0.85rem;margin:4px 0"><?= ip_h((string) ($r['notes'] ?: $r['category'])) ?></p>
                                <span class="ft-pill is-active" style="cursor:default;font-size:0.7rem"><?= (int) $r['duration'] ?> min · <?= (int) $r['calories'] ?> kcal</span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </aside>
    </div>

    <section class="ft-page-enter" style="margin-top:var(--space-7)" aria-labelledby="ip-features-heading">
        <h2 id="ip-features-heading" class="ip-section-title">Built for serious training</h2>
        <p class="ip-section-lead">Structured logging, routines, and nutrition tools in one calm workspace—so you can focus on execution.</p>
        <div class="ip-features">
            <article class="ip-feature-card">
                <div class="ip-feature-card__icon" aria-hidden="true"><i class="fas fa-chart-line"></i></div>
                <h3>Progress you can see</h3>
                <p>Charts and weekly summaries turn raw sessions into momentum—spot trends early and adjust with confidence.</p>
            </article>
            <article class="ip-feature-card">
                <div class="ip-feature-card__icon" aria-hidden="true"><i class="fas fa-layer-group"></i></div>
                <h3>Routines that scale</h3>
                <p>Save templates, duplicate blocks, and keep exercise metadata consistent across programs and training phases.</p>
            </article>
            <article class="ip-feature-card">
                <div class="ip-feature-card__icon" aria-hidden="true"><i class="fas fa-shield-halved"></i></div>
                <h3>Your data, your account</h3>
                <p>Session-backed PHP mode keeps ownership clear—export-friendly lists and a focused UI without noisy clutter.</p>
            </article>
        </div>
    </section>

    <section class="ft-page-enter" style="margin-top:var(--space-7)" aria-labelledby="ip-testimonials-heading">
        <h2 id="ip-testimonials-heading" class="ip-section-title">What athletes say</h2>
        <p class="ip-section-lead">Short feedback loops keep the product honest—here is how members describe the workflow.</p>
        <div class="ip-testimonials">
            <figure class="ip-quote">
                <blockquote>“The dashboard makes my week legible at a glance. I stopped guessing whether I was under-training.”</blockquote>
                <figcaption>Jordan M.<span>Hybrid athlete · 4x weekly</span></figcaption>
            </figure>
            <figure class="ip-quote">
                <blockquote>“Routines + workouts together finally replaced my scattered notes. The UI feels calm when I am not.”</blockquote>
                <figcaption>Alex R.<span>Strength coach · early adopter</span></figcaption>
            </figure>
        </div>
    </section>
</main>
<script>window.__IP_CHART_SERIES__ = <?= json_encode($calSeries ?: [0, 0, 0, 0, 0, 0, 0], JSON_THROW_ON_ERROR) ?>;</script>
<script>
document.addEventListener("DOMContentLoaded",function(){
  document.querySelectorAll("[data-counter]").forEach(function(el){
    var t=+el.getAttribute("data-counter"); if(isNaN(t)) return;
    var n=0, d=900, t0=performance.now();
    function fr(now){ var p=Math.min(1,(now-t0)/d); var e=1-Math.pow(1-p,3); el.textContent=String(Math.round(t*e)); if(p<1) requestAnimationFrame(fr); }
    requestAnimationFrame(fr);
  });
});
</script>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
