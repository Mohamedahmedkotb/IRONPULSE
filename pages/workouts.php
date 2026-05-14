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

$weTableExists = false;
try {
    $weTableExists = (bool) $pdo->query("SHOW TABLES LIKE 'workout_exercises'")->fetch();
} catch (Throwable $e) {
    $weTableExists = false;
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editRow = null;
if ($editId > 0) {
    $st = $pdo->prepare('SELECT * FROM workouts WHERE id = ? AND user_id = ? LIMIT 1');
    $st->execute([$editId, $userId]);
    $editRow = $st->fetch() ?: null;
}

$editExercises = [];
if ($weTableExists && $editRow) {
    $exSt = $pdo->prepare(
        'SELECT we.id AS we_id, we.sets, we.reps, e.id AS exercise_id, e.name
         FROM workout_exercises we
         INNER JOIN exercises e ON e.id = we.exercise_id
         WHERE we.workout_id = ?
         ORDER BY we.sort_order ASC, we.id ASC',
    );
    $exSt->execute([(int) $editRow['id']]);
    $editExercises = $exSt->fetchAll();
}

// ── Filters ──────────────────────────────────────────────────────────────────
$q = trim((string) ($_GET['q'] ?? ''));
$from = trim((string) ($_GET['from'] ?? ''));
$to = trim((string) ($_GET['to'] ?? ''));

$where = ['w.user_id = ?'];
$params = [$userId];

if ($q !== '') {
    $where[] = '(w.title LIKE ? OR w.category LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($from !== '') {
    $where[] = 'w.workout_date >= ?';
    $params[] = $from;
}
if ($to !== '') {
    $where[] = 'w.workout_date <= ?';
    $params[] = $to;
}

if ($weTableExists) {
    $sql = 'SELECT w.*, (SELECT COUNT(*) FROM workout_exercises we WHERE we.workout_id = w.id) AS exercise_count
            FROM workouts w WHERE ' . implode(' AND ', $where) . ' ORDER BY w.workout_date DESC, w.id DESC';
} else {
    $sql = 'SELECT w.*, 0 AS exercise_count FROM workouts w WHERE ' . implode(' AND ', $where) . ' ORDER BY w.workout_date DESC, w.id DESC';
}
$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();

// ── Compute Stats ────────────────────────────────────────────────────────────
$stats = ['count' => 0, 'duration' => 0, 'calories' => 0];
foreach ($rows as $r) {
    $stats['count']++;
    $stats['duration'] += (int) $r['duration'];
    $stats['calories'] += (int) $r['calories'];
}

$pageTitle = 'Workouts';
$currentNav = 'workouts';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<main class="app-main">
    <?php if (!$weTableExists): ?>
        <div class="ft-surface" style="margin-bottom:var(--space-5);padding:var(--space-4);border-radius:var(--radius-lg);border:1px solid #f59e0b;background:rgba(245,158,11,0.08);color:var(--text-primary)">
            <strong>Workout exercises table missing.</strong>
            <span class="ft-muted" style="display:block;margin-top:0.5rem;font-size:0.9rem">
                Open phpMyAdmin, select your database, and run the SQL file
                <code style="background:var(--page-bg);padding:2px 6px;border-radius:4px">database/migrate_add_workout_exercises.sql</code>
                — then refresh this page to link library exercises to workouts.
            </span>
        </div>
    <?php endif; ?>
    <div class="ft-flex-between" style="align-items:flex-start;margin-bottom:var(--space-6);flex-wrap:wrap;gap:20px">
        <div>
            <h1 style="margin:0;font-size:1.75rem">Workouts</h1>
            <p class="ft-muted">Track your progress and intensity.</p>
        </div>
        <div class="ft-flex" style="gap:12px;flex-wrap:wrap">
            <div class="ft-card ft-flex" style="padding:12px 20px;gap:12px;align-items:center;min-width:140px;background:var(--surface)">
                <div style="width:40px;height:40px;border-radius:50%;background:rgba(59,130,246,0.1);display:flex;align-items:center;justify-content:center;color:#3b82f6">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase">Workouts</div>
                    <div style="font-size:1.1rem;font-weight:700"><?= (int) $stats['count'] ?></div>
                </div>
            </div>
            <div class="ft-card ft-flex" style="padding:12px 20px;gap:12px;align-items:center;min-width:140px;background:var(--surface)">
                <div style="width:40px;height:40px;border-radius:50%;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;color:#10b981">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase">Duration</div>
                    <div style="font-size:1.1rem;font-weight:700"><?= (int) $stats['duration'] ?> <small style="font-size:0.7rem">min</small></div>
                </div>
            </div>
            <div class="ft-card ft-flex" style="padding:12px 20px;gap:12px;align-items:center;min-width:140px;background:var(--surface)">
                <div style="width:40px;height:40px;border-radius:50%;background:rgba(249,115,22,0.1);display:flex;align-items:center;justify-content:center;color:#f97316">
                    <i class="fas fa-fire"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase">Calories</div>
                    <div style="font-size:1.1rem;font-weight:700"><?= number_format($stats['calories']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($msg = ip_flash_get('success')): ?>
        <div style="padding:1rem;background:rgba(16,185,129,0.1);color:#10b981;border:1px solid #10b981;border-radius:var(--radius-md);margin-bottom:var(--space-5)">
            <i class="fas fa-check-circle" style="margin-right:8px"></i> <?= ip_h($msg) ?>
        </div>
    <?php endif; ?>
    <?php if ($msg = ip_flash_get('error')): ?>
        <div style="padding:1rem;background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid #ef4444;border-radius:var(--radius-md);margin-bottom:var(--space-5)">
            <i class="fas fa-exclamation-circle" style="margin-right:8px"></i> <?= ip_h($msg) ?>
        </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:var(--space-6);align-items:start">
        <div style="display:grid;gap:var(--space-6)">
            <!-- Filter Section -->
            <section class="ft-surface" style="padding:var(--space-5);border-radius:var(--radius-lg);border:1px solid var(--border)">
                <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
                    <div style="flex:1;min-width:200px">
                        <label class="ft-label">Search</label>
                        <input type="text" name="q" class="ft-input" style="width:100%" placeholder="Title or category..." value="<?= ip_h($q) ?>">
                    </div>
                    <div style="width:140px">
                        <label class="ft-label">From</label>
                        <input type="date" name="from" class="ft-input" style="width:100%" value="<?= ip_h($from) ?>">
                    </div>
                    <div style="width:140px">
                        <label class="ft-label">To</label>
                        <input type="date" name="to" class="ft-input" style="width:100%" value="<?= ip_h($to) ?>">
                    </div>
                    <button type="submit" class="ft-btn ft-btn--secondary" style="height:42px">Filter</button>
                    <?php if ($q || $from || $to): ?>
                        <a href="workouts.php" class="ft-btn ft-btn--ghost" style="height:42px;display:flex;align-items:center">Reset</a>
                    <?php endif; ?>
                </form>
            </section>

            <!-- Table Section -->
            <div class="ft-surface" style="border-radius:var(--radius-lg);border:1px solid var(--border);overflow:hidden">
                <table style="width:100%;border-collapse:collapse;font-size:0.9rem">
                    <thead>
                        <tr style="text-align:left;background:var(--page-bg);border-bottom:1px solid var(--border)">
                            <th style="padding:1rem">Date</th>
                            <th style="padding:1rem">Workout</th>
                            <th style="padding:1rem">Duration</th>
                            <th style="padding:1rem">Burn</th>
                            <th style="padding:1rem">Exercises</th>
                            <th style="padding:1rem;text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r): ?>
                            <tr style="border-bottom:1px solid var(--border)">
                                <td style="padding:0.75rem 1rem">
                                    <div style="font-weight:600"><?= ip_h(date('M d, Y', strtotime($r['workout_date']))) ?></div>
                                    <div style="font-size:0.75rem;color:var(--text-muted)"><?= ip_h($r['category']) ?></div>
                                </td>
                                <td style="padding:0.75rem 1rem">
                                    <div style="font-weight:600;color:var(--primary)"><?= ip_h((string) $r['title']) ?></div>
                                    <?php if ($r['notes']): ?>
                                        <div style="font-size:0.8rem;color:var(--text-muted);max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= ip_h($r['notes']) ?>">
                                            <?= ip_h($r['notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:0.75rem 1rem"><?= (int) $r['duration'] ?> <span class="ft-muted">min</span></td>
                                <td style="padding:0.75rem 1rem"><?= (int) $r['calories'] ?> <span class="ft-muted">kcal</span></td>
                                <td style="padding:0.75rem 1rem">
                                    <span style="font-weight:600"><?= (int) ($r['exercise_count'] ?? 0) ?></span>
                                    <span class="ft-muted" style="font-size:0.8rem"> linked</span>
                                </td>
                                <td style="padding:0.75rem 1rem;text-align:right">
                                    <div class="ft-flex" style="justify-content:flex-end;gap:8px">
                                        <a href="<?= ip_h(ip_url('pages/workouts.php?edit=' . (int) $r['id'])) ?>" class="ft-btn ft-btn--ghost" style="padding:4px 8px" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form method="post" action="<?= ip_h(ip_url('actions/delete_workout.php')) ?>" style="display:inline" onsubmit="return confirm('Delete this workout?');">
                                            <?= ip_csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                            <button type="submit" class="ft-btn ft-btn--ghost" style="padding:4px 8px;color:var(--danger)" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$rows): ?>
                            <tr><td colspan="6" style="padding:4rem;text-align:center" class="ft-muted">
                                <div style="font-size:2rem;margin-bottom:1rem">🏋️</div>
                                No workouts found matching your filters.
                            </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display:grid;gap:var(--space-6)">
            <?php if ($editRow): ?>
                <section class="ft-surface" style="padding:var(--space-5);border-radius:var(--radius-lg);border:1px solid var(--border)">
                    <h2 style="margin-top:0;font-size:1.1rem">Edit workout</h2>
                    <?php if ($weTableExists): ?>
                    <p class="ft-muted" style="margin:0 0 var(--space-4);font-size:0.9rem">
                        <a href="<?= ip_h(ip_url('pages/exercises.php?for_workout=' . (int) $editRow['id'])) ?>">Open exercise library</a>
                        to attach movements to this session.
                    </p>
                    <?php if ($editExercises !== []): ?>
                        <div style="margin-bottom:var(--space-5);border:1px solid var(--border);border-radius:var(--radius-md);overflow:hidden">
                            <table style="width:100%;border-collapse:collapse;font-size:0.85rem">
                                <thead>
                                    <tr style="background:var(--page-bg);text-align:left">
                                        <th style="padding:0.6rem 0.75rem">Exercise</th>
                                        <th style="padding:0.6rem 0.75rem">Sets × reps</th>
                                        <th style="padding:0.6rem 0.75rem;text-align:right">Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($editExercises as $we): ?>
                                        <tr style="border-top:1px solid var(--border)">
                                            <td style="padding:0.6rem 0.75rem;font-weight:600"><?= ip_h((string) $we['name']) ?></td>
                                            <td style="padding:0.6rem 0.75rem"><?= (int) $we['sets'] ?> × <?= (int) $we['reps'] ?></td>
                                            <td style="padding:0.6rem 0.75rem;text-align:right">
                                                <form method="post" action="<?= ip_h(ip_url('actions/remove_workout_exercise.php')) ?>" style="display:inline" onsubmit="return confirm('Remove this exercise from the workout?');">
                                                    <?= ip_csrf_field() ?>
                                                    <input type="hidden" name="workout_id" value="<?= (int) $editRow['id'] ?>">
                                                    <input type="hidden" name="we_id" value="<?= (int) $we['we_id'] ?>">
                                                    <button type="submit" class="ft-btn ft-btn--ghost" style="padding:2px 8px;font-size:0.8rem;color:var(--danger)" title="Remove exercise"><i class="fas fa-times"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="ft-muted" style="margin:0 0 var(--space-4);font-size:0.875rem">No exercises linked yet.</p>
                    <?php endif; ?>
                    <?php else: ?>
                        <p class="ft-muted" style="margin:0 0 var(--space-4);font-size:0.875rem">
                            Run <code style="font-size:0.85em">database/migrate_add_workout_exercises.sql</code> in MySQL to enable linking exercises from the library.
                        </p>
                    <?php endif; ?>
                    <form method="post" action="<?= ip_h(ip_url('actions/edit_workout.php')) ?>">
                        <?= ip_csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
                        <div style="display:grid;gap:1rem">
                            <div><label class="ft-label">Title</label><input class="ft-input" style="width:100%" name="title" required value="<?= ip_h((string) $editRow['title']) ?>"></div>
                            <div><label class="ft-label">Category</label><input class="ft-input" style="width:100%" name="category" value="<?= ip_h((string) $editRow['category']) ?>"></div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                                <div><label class="ft-label">Duration</label><input class="ft-input" style="width:100%" name="duration" type="number" value="<?= (int) $editRow['duration'] ?>"></div>
                                <div><label class="ft-label">Calories</label><input class="ft-input" style="width:100%" name="calories" type="number" value="<?= (int) $editRow['calories'] ?>"></div>
                            </div>
                            <div><label class="ft-label">Date</label><input class="ft-input" style="width:100%" name="workout_date" type="date" required value="<?= ip_h((string) $editRow['workout_date']) ?>"></div>
                            <div><label class="ft-label">Notes</label><textarea class="ft-input" style="width:100%;min-height:72px" name="notes"><?= ip_h((string) ($editRow['notes'] ?? '')) ?></textarea></div>
                        </div>
                        <div style="margin-top:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:10px">
                            <button type="submit" class="ft-btn ft-btn--primary">Save</button>
                            <a class="ft-btn ft-btn--secondary" style="text-align:center" href="<?= ip_h(ip_url('pages/workouts.php')) ?>">Cancel</a>
                        </div>
                    </form>
                </section>
            <?php else: ?>
                <section class="ft-surface" style="padding:var(--space-5);border-radius:var(--radius-lg);border:1px solid var(--border)">
                    <h2 style="margin-top:0;font-size:1.1rem">Log a workout</h2>
                    <form method="post" action="<?= ip_h(ip_url('actions/add_workout.php')) ?>">
                        <?= ip_csrf_field() ?>
                        <div style="display:grid;gap:1rem">
                            <div><label class="ft-label">Title</label><input class="ft-input" style="width:100%" name="title" required placeholder="Morning Run"></div>
                            <div><label class="ft-label">Category</label><input class="ft-input" style="width:100%" name="category" placeholder="Cardio"></div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                                <div><label class="ft-label">Min</label><input class="ft-input" style="width:100%" name="duration" type="number" value="30"></div>
                                <div><label class="ft-label">Kcal</label><input class="ft-input" style="width:100%" name="calories" type="number" value="200"></div>
                            </div>
                            <div><label class="ft-label">Date</label><input class="ft-input" style="width:100%" name="workout_date" type="date" required value="<?= ip_h(date('Y-m-d')) ?>"></div>
                            <div><label class="ft-label">Notes</label><textarea class="ft-input" style="width:100%;min-height:60px" name="notes"></textarea></div>
                        </div>
                        <button type="submit" class="ft-btn ft-btn--primary" style="margin-top:1.5rem;width:100%">Add workout</button>
                    </form>
                </section>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
