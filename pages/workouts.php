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
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editRow = null;
if ($editId > 0) {
    $st = $pdo->prepare('SELECT * FROM workouts WHERE id = ? AND user_id = ? LIMIT 1');
    $st->execute([$editId, $userId]);
    $editRow = $st->fetch() ?: null;
}

$st = $pdo->prepare(
    'SELECT id, title, category, duration, calories, workout_date, notes FROM workouts WHERE user_id = ? ORDER BY workout_date DESC, id DESC',
);
$st->execute([$userId]);
$rows = $st->fetchAll();

$pageTitle = 'Workouts';
$currentNav = 'workouts';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<main class="app-main">
    <h1 style="margin-top:0">Workouts</h1>
    <p class="ft-muted">Add, edit, or delete workouts with HTML forms—no fetch API.</p>

    <?php if ($editRow): ?>
        <section class="ft-surface" style="margin:var(--space-5) 0;padding:var(--space-5);border-radius:var(--radius-lg);border:1px solid var(--border)">
            <h2 style="margin-top:0;font-size:1.1rem">Edit workout</h2>
            <form method="post" action="<?= ip_h(ip_url('actions/edit_workout.php')) ?>">
                <?= ip_csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
                <div style="display:grid;gap:1rem;grid-template-columns:repeat(auto-fill,minmax(180px,1fr))">
                    <div><label class="ft-label">Title</label><input class="ft-input" style="width:100%" name="title" required value="<?= ip_h((string) $editRow['title']) ?>"></div>
                    <div><label class="ft-label">Category</label><input class="ft-input" style="width:100%" name="category" value="<?= ip_h((string) $editRow['category']) ?>"></div>
                    <div><label class="ft-label">Duration (min)</label><input class="ft-input" style="width:100%" name="duration" type="number" min="0" value="<?= (int) $editRow['duration'] ?>"></div>
                    <div><label class="ft-label">Calories</label><input class="ft-input" style="width:100%" name="calories" type="number" min="0" value="<?= (int) $editRow['calories'] ?>"></div>
                    <div><label class="ft-label">Date</label><input class="ft-input" style="width:100%" name="workout_date" type="date" required value="<?= ip_h((string) $editRow['workout_date']) ?>"></div>
                </div>
                <div style="margin-top:1rem"><label class="ft-label">Notes</label><textarea class="ft-input" style="width:100%;min-height:72px" name="notes"><?= ip_h((string) ($editRow['notes'] ?? '')) ?></textarea></div>
                <button type="submit" class="ft-btn ft-btn--primary" style="margin-top:1rem">Save</button>
                <a class="ft-btn ft-btn--secondary" style="margin-top:1rem;margin-left:0.5rem" href="<?= ip_h(ip_url('pages/workouts.php')) ?>">Cancel</a>
            </form>
        </section>
    <?php endif; ?>

    <section class="ft-surface" style="margin:var(--space-5) 0;padding:var(--space-5);border-radius:var(--radius-lg);border:1px solid var(--border)">
        <h2 style="margin-top:0;font-size:1.1rem">Log a workout</h2>
        <form method="post" action="<?= ip_h(ip_url('actions/add_workout.php')) ?>">
            <?= ip_csrf_field() ?>
            <div style="display:grid;gap:1rem;grid-template-columns:repeat(auto-fill,minmax(160px,1fr))">
                <div><label class="ft-label">Title</label><input class="ft-input" style="width:100%" name="title" required placeholder="Push day"></div>
                <div><label class="ft-label">Category</label><input class="ft-input" style="width:100%" name="category" placeholder="Strength"></div>
                <div><label class="ft-label">Duration (min)</label><input class="ft-input" style="width:100%" name="duration" type="number" min="0" value="45"></div>
                <div><label class="ft-label">Calories</label><input class="ft-input" style="width:100%" name="calories" type="number" min="0" value="300"></div>
                <div><label class="ft-label">Date</label><input class="ft-input" style="width:100%" name="workout_date" type="date" required value="<?= ip_h(date('Y-m-d')) ?>"></div>
            </div>
            <div style="margin-top:1rem"><label class="ft-label">Notes</label><textarea class="ft-input" style="width:100%;min-height:60px" name="notes"></textarea></div>
            <button type="submit" class="ft-btn ft-btn--primary" style="margin-top:1rem">Add workout</button>
        </form>
    </section>

    <div class="ft-surface" style="border-radius:var(--radius-lg);border:1px solid var(--border);overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:0.9rem">
            <thead>
                <tr style="text-align:left;background:var(--page-bg);border-bottom:1px solid var(--border)">
                    <th style="padding:0.75rem 1rem">Date</th>
                    <th style="padding:0.75rem 1rem">Title</th>
                    <th style="padding:0.75rem 1rem">Cat</th>
                    <th style="padding:0.75rem 1rem">Min</th>
                    <th style="padding:0.75rem 1rem">Kcal</th>
                    <th style="padding:0.75rem 1rem"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr style="border-bottom:1px solid var(--border)">
                        <td style="padding:0.65rem 1rem"><?= ip_h((string) $r['workout_date']) ?></td>
                        <td style="padding:0.65rem 1rem"><?= ip_h((string) $r['title']) ?></td>
                        <td style="padding:0.65rem 1rem"><?= ip_h((string) $r['category']) ?></td>
                        <td style="padding:0.65rem 1rem"><?= (int) $r['duration'] ?></td>
                        <td style="padding:0.65rem 1rem"><?= (int) $r['calories'] ?></td>
                        <td style="padding:0.65rem 1rem;white-space:nowrap">
                            <a href="<?= ip_h(ip_url('pages/workouts.php?edit=' . (int) $r['id'])) ?>">Edit</a>
                            <form method="post" action="<?= ip_h(ip_url('actions/delete_workout.php')) ?>" style="display:inline;margin-left:0.5rem" onsubmit="return confirm('Delete this workout?');">
                                <?= ip_csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <button type="submit" class="ft-btn ft-btn--ghost" style="padding:0.25rem 0.5rem;color:var(--danger,#b91c1c)">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?>
                    <tr><td colspan="6" style="padding:1.5rem" class="ft-muted">No workouts yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
