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
$weTableExists = false;
try {
    $weTableExists = (bool) $pdo->query("SHOW TABLES LIKE 'workout_exercises'")->fetch();
} catch (Throwable $e) {
    $weTableExists = false;
}

$search = trim((string) ($_GET['search'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
$muscle = trim((string) ($_GET['muscle'] ?? ''));

$where = [];
$params = [];

if ($search !== '') {
    $where[] = 'name LIKE ?';
    $params[] = '%' . $search . '%';
}
if ($category !== '') {
    $where[] = 'category = ?';
    $params[] = $category;
}
if ($muscle !== '') {
    $where[] = 'muscle_group = ?';
    $params[] = $muscle;
}

$sql = 'SELECT id, name, category, muscle_group, difficulty, instructions, image FROM exercises';
if ($where !== []) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY name ASC LIMIT 200';

$st = $pdo->prepare($sql);
$st->execute($params);
$exercises = $st->fetchAll();

// Fetch filter options
$categories = $pdo->query('SELECT DISTINCT category FROM exercises WHERE category != "" ORDER BY category ASC')->fetchAll(PDO::FETCH_COLUMN);
$muscles = $pdo->query('SELECT DISTINCT muscle_group FROM exercises WHERE muscle_group != "" ORDER BY muscle_group ASC')->fetchAll(PDO::FETCH_COLUMN);

$forWorkout = isset($_GET['for_workout']) ? (int) $_GET['for_workout'] : 0;
$workoutsSt = $pdo->prepare('SELECT id, title, workout_date FROM workouts WHERE user_id = ? ORDER BY workout_date DESC, id DESC LIMIT 100');
$workoutsSt->execute([$userId]);
$workoutsList = $workoutsSt->fetchAll();
$forWorkoutValid = false;
if ($forWorkout > 0) {
    foreach ($workoutsList as $rw) {
        if ((int) $rw['id'] === $forWorkout) {
            $forWorkoutValid = true;
            break;
        }
    }
    if (!$forWorkoutValid) {
        $forWorkout = 0;
    }
}

$pageTitle = 'Exercise library';
$currentNav = 'exercises';

require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/sidebar.php';
require_once dirname(__DIR__) . '/includes/navbar.php';
?>
<main class="app-main">
    <h1 style="margin-top:0">Exercises</h1>
    <p class="ft-muted">Search, filter, and preview movement patterns. Add any row to one of your workouts.</p>

    <?php if (!$weTableExists): ?>
        <div class="ft-surface" style="margin-top:var(--space-4);padding:var(--space-4);border-radius:var(--radius-lg);border:1px solid #f59e0b;background:rgba(245,158,11,0.08)">
            <strong>Workout exercises not enabled.</strong>
            <p class="ft-muted" style="margin:0.5rem 0 0;font-size:0.9rem">
                Run <code style="background:var(--page-bg);padding:2px 6px;border-radius:4px">database/migrate_add_workout_exercises.sql</code> in phpMyAdmin (your <code>ironpulse_db</code> database), then reload this page to use “Add to workout”.
            </p>
        </div>
    <?php endif; ?>

    <?php if ($forWorkout > 0 && $weTableExists): ?>
        <div class="ft-surface" style="margin-top:var(--space-4);padding:var(--space-4);border-radius:var(--radius-lg);border:1px solid var(--border);background:var(--accent-soft)">
            <strong>Adding to a workout</strong>
            <span class="ft-muted" style="font-size:0.9rem"> — choose a session below each exercise, set optional sets/reps, then <em>Add</em>. Return to </span>
            <a href="<?= ip_h(ip_url('pages/workouts.php?edit=' . $forWorkout)) ?>">edit workout</a>.
        </div>
    <?php endif; ?>

    <div class="ft-surface" style="margin-top:var(--space-5); padding:1.25rem; border-radius:var(--radius-lg); border:1px solid var(--border)">
        <form action="" method="GET" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end">
            <?php if ($forWorkout > 0): ?>
                <input type="hidden" name="for_workout" value="<?= (int) $forWorkout ?>">
            <?php endif; ?>
            <div style="flex:1; min-width:200px">
                <label style="display:block; font-size:0.75rem; font-weight:600; margin-bottom:4px; color:var(--text-muted)">SEARCH</label>
                <input type="text" name="search" value="<?= ip_h($search) ?>" placeholder="e.g. Bench press" style="width:100%; padding:0.5rem 0.75rem; border-radius:var(--radius-md); border:1px solid var(--border); background:var(--page-bg); color:var(--text)">
            </div>
            <div style="width:160px">
                <label style="display:block; font-size:0.75rem; font-weight:600; margin-bottom:4px; color:var(--text-muted)">CATEGORY</label>
                <select name="category" style="width:100%; padding:0.5rem 0.75rem; border-radius:var(--radius-md); border:1px solid var(--border); background:var(--page-bg); color:var(--text)">
                    <option value="">All Types</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= ip_h($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= ip_h($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="width:160px">
                <label style="display:block; font-size:0.75rem; font-weight:600; margin-bottom:4px; color:var(--text-muted)">MUSCLE</label>
                <select name="muscle" style="width:100%; padding:0.5rem 0.75rem; border-radius:var(--radius-md); border:1px solid var(--border); background:var(--page-bg); color:var(--text)">
                    <option value="">All Muscles</option>
                    <?php foreach ($muscles as $m): ?>
                        <option value="<?= ip_h($m) ?>" <?= $muscle === $m ? 'selected' : '' ?>><?= ip_h($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="ft-btn ft-btn--primary" style="height:38px; padding:0 1.25rem">Filter</button>
            <?php if ($search !== '' || $category !== '' || $muscle !== ''): ?>
                <a href="<?= ip_h(ip_url('pages/exercises.php' . ($forWorkout > 0 ? '?for_workout=' . $forWorkout : ''))) ?>" style="font-size:0.85rem; color:var(--text-muted); text-decoration:none; margin-bottom:10px">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="ft-surface" style="margin-top:var(--space-4); border-radius:var(--radius-lg); border:1px solid var(--border); overflow:auto">
        <table style="width:100%;border-collapse:collapse;font-size:0.9rem;min-width:640px">
            <thead>
                <tr style="text-align:left;background:var(--page-bg);border-bottom:1px solid var(--border)">
                    <th style="padding:0.75rem 1rem">Name</th>
                    <th style="padding:0.75rem 1rem">Category</th>
                    <th style="padding:0.75rem 1rem">Muscle</th>
                    <th style="padding:0.75rem 1rem">Level</th>
                    <?php if ($weTableExists): ?>
                    <th style="padding:0.75rem 1rem;min-width:220px">Add to workout</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exercises as $e): ?>
                    <tr style="border-bottom:1px solid var(--border)">
                        <td style="padding:0.6rem 1rem">
                            <div style="font-weight:600"><?= ip_h((string) $e['name']) ?></div>
                            <?php 
                            $img = (string) $e['image'];
                            if (!empty($img)): 
                                // Construct URL: if it looks like an Unsplash ID, use Unsplash; otherwise use local assets
                                $src = (str_contains($img, '/') || str_contains($img, '.')) 
                                    ? ip_url('assets/media/' . $img) 
                                    : "https://images.unsplash.com/{$img}?auto=format&fit=crop&w=600&q=80";
                            ?>
                                <img src="<?= ip_h($src) ?>" alt="<?= ip_h($e['name']) ?>" style="display:block;margin-top:8px;max-width:160px;border-radius:var(--radius-md);border:1px solid var(--border);background:var(--page-bg)">
                            <?php endif; ?>
                        </td>
                        <td style="padding:0.6rem 1rem"><?= ip_h((string) $e['category']) ?></td>
                        <td style="padding:0.6rem 1rem"><?= ip_h((string) $e['muscle_group']) ?></td>
                        <td style="padding:0.6rem 1rem"><?= ip_h((string) $e['difficulty']) ?></td>
                        <?php if ($weTableExists): ?>
                        <td style="padding:0.6rem 1rem;vertical-align:top">
                            <?php if ($workoutsList !== []): ?>
                                <form method="post" action="<?= ip_h(ip_url('actions/attach_exercise_to_workout.php')) ?>" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">
                                    <?= ip_csrf_field() ?>
                                    <input type="hidden" name="exercise_id" value="<?= (int) $e['id'] ?>">
                                    <select name="workout_id" class="ft-input" style="flex:1;min-width:140px;font-size:0.8rem;padding:0.35rem 0.5rem" required>
                                        <?php foreach ($workoutsList as $wo): ?>
                                            <option value="<?= (int) $wo['id'] ?>" <?= $forWorkout === (int) $wo['id'] ? 'selected' : '' ?>>
                                                <?= ip_h(date('M j', strtotime((string) $wo['workout_date'])) . ' — ' . mb_substr((string) $wo['title'], 0, 40)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="number" name="sets" value="3" min="1" max="99" title="Sets" class="ft-input" style="width:52px;font-size:0.8rem;padding:0.35rem" aria-label="Sets">
                                    <span class="ft-muted" style="font-size:0.75rem">×</span>
                                    <input type="number" name="reps" value="10" min="1" max="999" title="Reps" class="ft-input" style="width:56px;font-size:0.8rem;padding:0.35rem" aria-label="Reps">
                                    <button type="submit" class="ft-btn ft-btn--primary ft-btn--sm" style="white-space:nowrap">Add</button>
                                </form>
                            <?php else: ?>
                                <span class="ft-muted" style="font-size:0.85rem"><a href="<?= ip_h(ip_url('pages/workouts.php')) ?>">Log a workout</a> first.</span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                <?php if ($exercises === []): ?>
                    <tr>
                        <td colspan="<?= $weTableExists ? 5 : 4 ?>" style="padding:3rem; text-align:center; color:var(--text-muted)">
                            <div style="font-size:1.5rem; margin-bottom:0.5rem">🔍</div>
                            No exercises found matching your criteria.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
