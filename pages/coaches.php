<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
ip_require_login();

$currentUser = ip_current_user($pdo);
if (!$currentUser) {
    session_destroy();
    ip_redirect('pages/login.php');
}

$st = $pdo->query('SELECT id, name, specialty, bio, image, rating FROM coaches ORDER BY id ASC');
$coaches = $st->fetchAll();

$pageTitle = 'Coaches';
$currentNav = 'coaches';

require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/sidebar.php';
require_once dirname(__DIR__) . '/includes/navbar.php';
?>
<main class="app-main">
    <h1 style="margin-top:0">Elite coaches</h1>
    <p class="ft-muted">Book a session using a POST form (stored in <code>bookings</code>).</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:var(--space-5);margin-top:var(--space-5)">
        <?php foreach ($coaches as $c): ?>
            <article class="ft-surface" style="border-radius:var(--radius-lg);border:1px solid var(--border);overflow:hidden">
                <div style="height:140px;background:#0f172a center/cover url('<?= ip_h($c['image'] !== '' ? (string) $c['image'] : 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?auto=format&fit=crop&w=800&q=70') ?>')"></div>
                <div style="padding:var(--space-4)">
                    <h2 style="margin:0;font-size:1.1rem"><?= ip_h((string) $c['name']) ?></h2>
                    <p class="ft-muted" style="margin:0.25rem 0"><?= ip_h((string) $c['specialty']) ?> · <?= ip_h((string) $c['rating']) ?>★</p>
                    <p style="font-size:0.9rem;margin:0.5rem 0"><?= ip_h(mb_substr((string) ($c['bio'] ?? ''), 0, 160)) ?></p>
                    <form method="post" action="<?= ip_h(ip_url('actions/book_coach.php')) ?>" style="margin-top:var(--space-3)">
                        <?= ip_csrf_field() ?>
                        <input type="hidden" name="coach_id" value="<?= (int) $c['id'] ?>">
                        <label class="ft-label" for="bd<?= (int) $c['id'] ?>">Preferred time</label>
                        <input class="ft-input" style="width:100%;margin-bottom:0.5rem" id="bd<?= (int) $c['id'] ?>" name="booking_date" type="datetime-local">
                        <textarea class="ft-input" style="width:100%;min-height:48px;margin-bottom:0.5rem" name="notes" placeholder="Goals or injuries"></textarea>
                        <button type="submit" class="ft-btn ft-btn--primary ft-btn--block">Request booking</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
