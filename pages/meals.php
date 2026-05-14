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
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editRow = null;

if ($editId > 0) {
    $st = $pdo->prepare('SELECT * FROM meal_plans WHERE id = ? AND user_id = ? LIMIT 1');
    $st->execute([$editId, $userId]);
    $editRow = $st->fetch() ?: null;
}

// ── Filters ──────────────────────────────────────────────────────────────────
$q = trim((string) ($_GET['q'] ?? ''));
$from = trim((string) ($_GET['from'] ?? ''));
$to = trim((string) ($_GET['to'] ?? ''));

$where = ['user_id = ?'];
$params = [$userId];

if ($q !== '') {
    $where[] = '(title LIKE ? OR breakfast LIKE ? OR lunch LIKE ? OR dinner LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($from !== '') {
    $where[] = 'plan_date >= ?';
    $params[] = $from;
}
if ($to !== '') {
    $where[] = 'plan_date <= ?';
    $params[] = $to;
}

$sql = 'SELECT * FROM meal_plans WHERE ' . implode(' AND ', $where) . ' ORDER BY plan_date DESC, id DESC';
$st = $pdo->prepare($sql);
$st->execute($params);
$meals = $st->fetchAll();

// ── Compute Stats ────────────────────────────────────────────────────────────
$stats = ['count' => count($meals), 'calories' => 0, 'protein' => 0];
foreach ($meals as $m) {
    $stats['calories'] += (int) $m['calories'];
    $stats['protein'] += (int) $m['protein_g'];
}
$avgCalories = $stats['count'] > 0 ? round($stats['calories'] / $stats['count']) : 0;
$avgProtein = $stats['count'] > 0 ? round($stats['protein'] / $stats['count']) : 0;

$pageTitle = 'Meal Plans';
$currentNav = 'meals';

require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/sidebar.php';
require_once dirname(__DIR__) . '/includes/navbar.php';
?>
<main class="app-main">
    <div class="ft-flex-between" style="align-items:flex-start;margin-bottom:var(--space-6);flex-wrap:wrap;gap:20px">
        <div>
            <h1 style="margin:0;font-size:1.75rem">Meal Plans</h1>
            <p class="ft-muted">Track your nutrition and daily macros.</p>
        </div>
        <div class="ft-flex" style="gap:12px;flex-wrap:wrap">
            <div class="ft-card ft-flex" style="padding:12px 20px;gap:12px;align-items:center;min-width:140px;background:var(--surface)">
                <div style="width:40px;height:40px;border-radius:50%;background:rgba(59,130,246,0.1);display:flex;align-items:center;justify-content:center;color:#3b82f6">
                    <i class="fas fa-utensils"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase">Plans</div>
                    <div style="font-size:1.1rem;font-weight:700"><?= (int) $stats['count'] ?></div>
                </div>
            </div>
            <div class="ft-card ft-flex" style="padding:12px 20px;gap:12px;align-items:center;min-width:140px;background:var(--surface)">
                <div style="width:40px;height:40px;border-radius:50%;background:rgba(249,115,22,0.1);display:flex;align-items:center;justify-content:center;color:#f97316">
                    <i class="fas fa-fire"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase">Avg Calories</div>
                    <div style="font-size:1.1rem;font-weight:700"><?= number_format((float) $avgCalories) ?></div>
                </div>
            </div>
            <div class="ft-card ft-flex" style="padding:12px 20px;gap:12px;align-items:center;min-width:140px;background:var(--surface)">
                <div style="width:40px;height:40px;border-radius:50%;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;color:#10b981">
                    <i class="fas fa-drumstick-bite"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase">Avg Protein</div>
                    <div style="font-size:1.1rem;font-weight:700"><?= number_format((float) $avgProtein) ?> <small style="font-size:0.7rem">g</small></div>
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
                        <label class="ft-label">Search Meals</label>
                        <input type="text" name="q" class="ft-input" style="width:100%" placeholder="e.g. Chicken, Pasta..." value="<?= ip_h($q) ?>">
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
                        <a href="meals.php" class="ft-btn ft-btn--ghost" style="height:42px;display:flex;align-items:center">Reset</a>
                    <?php endif; ?>
                </form>
            </section>

            <!-- Meal List -->
            <div style="display:flex;flex-direction:column;gap:var(--space-4)">
                <?php foreach ($meals as $m): ?>
                    <article class="ft-surface" style="border-radius:var(--radius-lg);border:1px solid var(--border);padding:var(--space-5);display:flex;flex-direction:column;gap:12px">
                        <div class="ft-flex-between" style="align-items:flex-start">
                            <div>
                                <h3 style="margin:0 0 4px 0;font-size:1.2rem"><?= ip_h((string) $m['title']) ?></h3>
                                <div style="font-size:0.85rem;color:var(--text-muted);font-weight:600">
                                    <i class="far fa-calendar-alt" style="margin-right:4px"></i> <?= ip_h(date('F j, Y', strtotime($m['plan_date']))) ?>
                                </div>
                            </div>
                            <div class="ft-flex" style="gap:8px">
                                <a href="<?= ip_h(ip_url('pages/meals.php?edit=' . (int) $m['id'])) ?>" class="ft-btn ft-btn--ghost" style="padding:4px 8px" title="Edit"><i class="fas fa-edit"></i></a>
                                <form method="post" action="<?= ip_h(ip_url('actions/delete_meal.php')) ?>" style="display:inline" onsubmit="return confirm('Delete this meal plan?');">
                                    <?= ip_csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                                    <button type="submit" class="ft-btn ft-btn--ghost" style="padding:4px 8px;color:var(--danger)" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;background:var(--page-bg);padding:12px;border-radius:var(--radius-md);font-size:0.9rem">
                            <div>
                                <strong style="color:var(--text-muted);font-size:0.75rem;text-transform:uppercase">Breakfast</strong>
                                <div style="margin-top:2px"><?= ip_h((string) $m['breakfast'] ?: '—') ?></div>
                            </div>
                            <div>
                                <strong style="color:var(--text-muted);font-size:0.75rem;text-transform:uppercase">Lunch</strong>
                                <div style="margin-top:2px"><?= ip_h((string) $m['lunch'] ?: '—') ?></div>
                            </div>
                            <div>
                                <strong style="color:var(--text-muted);font-size:0.75rem;text-transform:uppercase">Dinner</strong>
                                <div style="margin-top:2px"><?= ip_h((string) $m['dinner'] ?: '—') ?></div>
                            </div>
                        </div>

                        <div class="ft-flex-between" style="border-top:1px dashed var(--border);padding-top:12px;font-size:0.85rem">
                            <span style="font-weight:700;color:var(--primary);font-size:1.1rem"><?= (int) $m['calories'] ?> <small style="font-weight:normal;color:var(--text-muted)">kcal</small></span>
                            <div class="ft-flex" style="gap:16px;color:var(--text-muted)">
                                <span><strong>P</strong> <?= (int) $m['protein_g'] ?>g</span>
                                <span><strong>C</strong> <?= (int) $m['carbs_g'] ?>g</span>
                                <span><strong>F</strong> <?= (int) $m['fats_g'] ?>g</span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
                
                <?php if (!$meals): ?>
                    <div style="padding:4rem 2rem;text-align:center;background:var(--surface);border:1px dashed var(--border);border-radius:var(--radius-lg)">
                        <div style="font-size:2.5rem;margin-bottom:1rem;color:var(--text-muted)">🥗</div>
                        <h3 style="margin:0 0 0.5rem 0">No Meal Plans Found</h3>
                        <p class="ft-muted" style="margin:0">Log a new meal plan to start tracking your nutrition.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add/Edit Form Sidebar -->
        <div style="display:grid;gap:var(--space-6)">
            <?php if ($editRow): ?>
                <section class="ft-surface" style="padding:var(--space-5);border-radius:var(--radius-lg);border:1px solid var(--border);position:sticky;top:20px">
                    <h2 style="margin-top:0;font-size:1.1rem">Edit Meal Plan</h2>
                    <form method="post" action="<?= ip_h(ip_url('actions/edit_meal.php')) ?>">
                        <?= ip_csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
                        <div style="display:grid;gap:1rem">
                            <div><label class="ft-label">Title</label><input class="ft-input" style="width:100%" name="title" required value="<?= ip_h((string) $editRow['title']) ?>"></div>
                            <div><label class="ft-label">Date</label><input class="ft-input" style="width:100%" name="plan_date" type="date" required value="<?= ip_h((string) $editRow['plan_date']) ?>"></div>
                            
                            <hr style="border:0;border-top:1px solid var(--border);margin:0">
                            
                            <div><label class="ft-label">Breakfast</label><input class="ft-input" style="width:100%" name="breakfast" value="<?= ip_h((string) $editRow['breakfast']) ?>"></div>
                            <div><label class="ft-label">Lunch</label><input class="ft-input" style="width:100%" name="lunch" value="<?= ip_h((string) $editRow['lunch']) ?>"></div>
                            <div><label class="ft-label">Dinner</label><input class="ft-input" style="width:100%" name="dinner" value="<?= ip_h((string) $editRow['dinner']) ?>"></div>
                            
                            <hr style="border:0;border-top:1px solid var(--border);margin:0">
                            
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                                <div><label class="ft-label">Calories</label><input class="ft-input" style="width:100%" name="calories" type="number" value="<?= (int) $editRow['calories'] ?>"></div>
                                <div><label class="ft-label">Protein (g)</label><input class="ft-input" style="width:100%" name="protein_g" type="number" value="<?= (int) $editRow['protein_g'] ?>"></div>
                                <div><label class="ft-label">Carbs (g)</label><input class="ft-input" style="width:100%" name="carbs_g" type="number" value="<?= (int) $editRow['carbs_g'] ?>"></div>
                                <div><label class="ft-label">Fats (g)</label><input class="ft-input" style="width:100%" name="fats_g" type="number" value="<?= (int) $editRow['fats_g'] ?>"></div>
                            </div>
                        </div>
                        <div style="margin-top:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:10px">
                            <button type="submit" class="ft-btn ft-btn--primary">Save Changes</button>
                            <a class="ft-btn ft-btn--secondary" style="text-align:center" href="<?= ip_h(ip_url('pages/meals.php')) ?>">Cancel</a>
                        </div>
                    </form>
                </section>
            <?php else: ?>
                <section class="ft-surface" style="padding:var(--space-5);border-radius:var(--radius-lg);border:1px solid var(--border);position:sticky;top:20px">
                    <h2 style="margin-top:0;font-size:1.1rem">Log a Meal Plan</h2>
                    <form method="post" action="<?= ip_h(ip_url('actions/add_meal.php')) ?>">
                        <?= ip_csrf_field() ?>
                        <div style="display:grid;gap:1rem">
                            <div><label class="ft-label">Title</label><input class="ft-input" style="width:100%" name="title" required placeholder="e.g. Rest Day Macros"></div>
                            <div><label class="ft-label">Date</label><input class="ft-input" style="width:100%" name="plan_date" type="date" required value="<?= ip_h(date('Y-m-d')) ?>"></div>
                            
                            <hr style="border:0;border-top:1px solid var(--border);margin:0">
                            
                            <div><label class="ft-label">Breakfast</label><input class="ft-input" style="width:100%" name="breakfast" placeholder="e.g. 3 Eggs, Oatmeal"></div>
                            <div><label class="ft-label">Lunch</label><input class="ft-input" style="width:100%" name="lunch" placeholder="e.g. Chicken breast, Rice"></div>
                            <div><label class="ft-label">Dinner</label><input class="ft-input" style="width:100%" name="dinner" placeholder="e.g. Salmon, Broccoli"></div>
                            
                            <hr style="border:0;border-top:1px solid var(--border);margin:0">
                            
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                                <div><label class="ft-label">Calories</label><input class="ft-input" style="width:100%" name="calories" type="number" placeholder="2500"></div>
                                <div><label class="ft-label">Protein (g)</label><input class="ft-input" style="width:100%" name="protein_g" type="number" placeholder="180"></div>
                                <div><label class="ft-label">Carbs (g)</label><input class="ft-input" style="width:100%" name="carbs_g" type="number" placeholder="250"></div>
                                <div><label class="ft-label">Fats (g)</label><input class="ft-input" style="width:100%" name="fats_g" type="number" placeholder="70"></div>
                            </div>
                        </div>
                        <button type="submit" class="ft-btn ft-btn--primary" style="margin-top:1.5rem;width:100%">Save Meal Plan</button>
                    </form>
                </section>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
