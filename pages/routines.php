<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

if (!function_exists('ip_routines_sort_order_clause')) {
    /**
     * ORDER BY clause for routines list (expects alias `r` and column `exercise_count`).
     */
    function ip_routines_sort_order_clause(string $sort): string
    {
        return match ($sort) {
            'oldest' => 'r.id ASC',
            'az' => 'r.title ASC',
            'za' => 'r.title DESC',
            'updated' => 'r.updated_at DESC, r.id DESC',
            'updated_asc' => 'r.updated_at ASC, r.id ASC',
            'ex_desc' => 'exercise_count DESC, r.id DESC',
            'ex_asc' => 'exercise_count ASC, r.id DESC',
            default => 'r.id DESC',
        };
    }

    /**
     * @return array{0: list<string>, 1: list<mixed>} WHERE fragments and bound parameters for routines alias `r`.
     */
    function ip_routines_list_where(int $userId, string $q, string $exFilter): array
    {
        $where = ['r.user_id = ?'];
        $params = [$userId];

        if ($q !== '') {
            $where[] = '(r.title LIKE ? OR r.description LIKE ?)';
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }

        if ($exFilter === 'nonempty') {
            $where[] = '(SELECT COUNT(*) FROM routine_exercises re WHERE re.routine_id = r.id) > 0';
        } elseif ($exFilter === 'empty') {
            $where[] = '(SELECT COUNT(*) FROM routine_exercises re WHERE re.routine_id = r.id) = 0';
        }

        return [$where, $params];
    }

    /**
     * Fetch exercise rows for previews, grouped by routine_id.
     *
     * @param list<int> $routineIds
     * @return array<int, list<array<string, mixed>>>
     */
    function ip_routines_exercises_grouped(PDO $pdo, array $routineIds): array
    {
        if ($routineIds === []) {
            return [];
        }

        $inPlaceholders = str_repeat('?,', count($routineIds) - 1) . '?';
        $exSql = "SELECT re.routine_id, e.name, re.sets, re.reps
                  FROM routine_exercises re
                  JOIN exercises e ON re.exercise_id = e.id
                  WHERE re.routine_id IN ($inPlaceholders)
                  ORDER BY re.routine_id, re.sort_order";
        $exSt = $pdo->prepare($exSql);
        $exSt->execute($routineIds);
        $grouped = [];
        foreach ($exSt->fetchAll() as $ex) {
            $rid = (int) $ex['routine_id'];
            $grouped[$rid][] = $ex;
        }

        return $grouped;
    }

    /**
     * Build relative URL query for routines list (drops default values).
     *
     * @param array<string, scalar|null> $params
     */
    function ip_routines_query_string(array $params): string
    {
        $defaults = ['sort' => 'newest', 'ex' => 'all', 'pp' => 12, 'page' => 1];
        $out = [];
        foreach ($params as $k => $v) {
            if ($v === null || $v === '') {
                continue;
            }
            if (isset($defaults[$k]) && $v == $defaults[$k]) {
                continue;
            }
            $out[$k] = $v;
        }

        return http_build_query($out);
    }
}

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
    $st = $pdo->prepare('SELECT * FROM routines WHERE id = ? AND user_id = ? LIMIT 1');
    $st->execute([$editId, $userId]);
    $editRow = $st->fetch() ?: null;
}

// ── Filters, sort, pagination ─────────────────────────────────────────────────
$q = trim((string) ($_GET['q'] ?? ''));
$sort = trim((string) ($_GET['sort'] ?? 'newest'));
$allowedSort = ['newest', 'oldest', 'az', 'za', 'updated', 'updated_asc', 'ex_desc', 'ex_asc'];
if (!in_array($sort, $allowedSort, true)) {
    $sort = 'newest';
}

$exFilter = trim((string) ($_GET['ex'] ?? 'all'));
if (!in_array($exFilter, ['all', 'nonempty', 'empty'], true)) {
    $exFilter = 'all';
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = (int) ($_GET['pp'] ?? 12);
if (!in_array($perPage, [6, 12, 24, 48], true)) {
    $perPage = 12;
}

[$where, $params] = ip_routines_list_where($userId, $q, $exFilter);
$whereSql = implode(' AND ', $where);

// Stats for current filter (all matching routines, not just this page)
$statsSql = 'SELECT COUNT(DISTINCT r.id) AS routine_count, COUNT(re.id) AS exercise_slots
             FROM routines r
             LEFT JOIN routine_exercises re ON re.routine_id = r.id
             WHERE ' . $whereSql;
$statsSt = $pdo->prepare($statsSql);
$statsSt->execute($params);
$statsRow = $statsSt->fetch() ?: ['routine_count' => 0, 'exercise_slots' => 0];
$totalFiltered = (int) $statsRow['routine_count'];
$totalPages = max(1, (int) ceil($totalFiltered / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

$orderClause = ip_routines_sort_order_clause($sort);
$sql = 'SELECT r.*, (SELECT COUNT(*) FROM routine_exercises re WHERE re.routine_id = r.id) AS exercise_count
        FROM routines r
        WHERE ' . $whereSql . '
        ORDER BY ' . $orderClause . '
        LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;

$st = $pdo->prepare($sql);
$st->execute($params);
$routines = $st->fetchAll();

$routineIds = array_column($routines, 'id');
$routineExercises = ip_routines_exercises_grouped($pdo, array_map('intval', $routineIds));

$stats = [
    'total_routines' => $totalFiltered,
    'total_exercises' => (int) $statsRow['exercise_slots'],
];
if ($stats['total_routines'] > 0) {
    $stats['avg_exercises'] = round($stats['total_exercises'] / $stats['total_routines'], 1);
} else {
    $stats['avg_exercises'] = 0.0;
}

$filtersActive = ($q !== '' || $sort !== 'newest' || $exFilter !== 'all' || $perPage !== 12 || $page > 1);

$pageTitle = 'Routines';
$currentNav = 'routines';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/navbar.php';

$mkListUrl = static function (array $extra = []) use ($q, $sort, $exFilter, $perPage, $page, $editId): string {
    $base = [
        'q' => $q,
        'sort' => $sort,
        'ex' => $exFilter,
        'pp' => $perPage,
        'page' => $page,
    ];
    if ($editId > 0) {
        $base['edit'] = $editId;
    }
    $merged = array_merge($base, $extra);
    $qs = ip_routines_query_string($merged);

    return 'routines.php' . ($qs !== '' ? '?' . $qs : '');
};
?>
<main class="app-main">
    <div class="ft-flex-between" style="align-items:flex-start;margin-bottom:var(--space-6);flex-wrap:wrap;gap:20px">
        <div>
            <h1 style="margin:0;font-size:1.75rem">Routines</h1>
            <p class="ft-muted">Manage your training programs and plans.</p>
        </div>
        <div class="ft-flex" style="gap:12px;flex-wrap:wrap">
            <div class="ft-card ft-flex" style="padding:12px 20px;gap:12px;align-items:center;min-width:140px;background:var(--surface)">
                <div style="width:40px;height:40px;border-radius:50%;background:rgba(59,130,246,0.1);display:flex;align-items:center;justify-content:center;color:#3b82f6">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase">Routines</div>
                    <div style="font-size:1.1rem;font-weight:700"><?= (int) $stats['total_routines'] ?></div>
                </div>
            </div>
            <div class="ft-card ft-flex" style="padding:12px 20px;gap:12px;align-items:center;min-width:140px;background:var(--surface)">
                <div style="width:40px;height:40px;border-radius:50%;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;color:#10b981">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase">Exercise slots</div>
                    <div style="font-size:1.1rem;font-weight:700"><?= (int) $stats['total_exercises'] ?></div>
                </div>
            </div>
            <div class="ft-card ft-flex" style="padding:12px 20px;gap:12px;align-items:center;min-width:140px;background:var(--surface)">
                <div style="width:40px;height:40px;border-radius:50%;background:rgba(249,115,22,0.1);display:flex;align-items:center;justify-content:center;color:#f97316">
                    <i class="fas fa-chart-simple"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem;font-weight:600;color:var(--text-muted);text-transform:uppercase">Avg / routine</div>
                    <div style="font-size:1.1rem;font-weight:700"><?= ip_h((string) $stats['avg_exercises']) ?></div>
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
                    <?php if ($editId > 0): ?>
                        <input type="hidden" name="edit" value="<?= (int) $editId ?>">
                    <?php endif; ?>
                    <div style="flex:1;min-width:200px">
                        <label class="ft-label">Search</label>
                        <input type="text" name="q" class="ft-input" style="width:100%" placeholder="e.g. Upper Body..." value="<?= ip_h($q) ?>">
                    </div>
                    <div style="width:160px">
                        <label class="ft-label">Exercises</label>
                        <select name="ex" class="ft-input" style="width:100%">
                            <option value="all" <?= $exFilter === 'all' ? 'selected' : '' ?>>All routines</option>
                            <option value="nonempty" <?= $exFilter === 'nonempty' ? 'selected' : '' ?>>With exercises</option>
                            <option value="empty" <?= $exFilter === 'empty' ? 'selected' : '' ?>>Empty (no exercises)</option>
                        </select>
                    </div>
                    <div style="width:160px">
                        <label class="ft-label">Sort</label>
                        <select name="sort" class="ft-input" style="width:100%">
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest first</option>
                            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest first</option>
                            <option value="updated" <?= $sort === 'updated' ? 'selected' : '' ?>>Recently updated</option>
                            <option value="updated_asc" <?= $sort === 'updated_asc' ? 'selected' : '' ?>>Least recently updated</option>
                            <option value="ex_desc" <?= $sort === 'ex_desc' ? 'selected' : '' ?>>Most exercises</option>
                            <option value="ex_asc" <?= $sort === 'ex_asc' ? 'selected' : '' ?>>Fewest exercises</option>
                            <option value="az" <?= $sort === 'az' ? 'selected' : '' ?>>Title (A–Z)</option>
                            <option value="za" <?= $sort === 'za' ? 'selected' : '' ?>>Title (Z–A)</option>
                        </select>
                    </div>
                    <div style="width:100px">
                        <label class="ft-label">Per page</label>
                        <select name="pp" class="ft-input" style="width:100%">
                            <?php foreach ([6, 12, 24, 48] as $n): ?>
                                <option value="<?= $n ?>" <?= $perPage === $n ? 'selected' : '' ?>><?= $n ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="page" value="1">
                    <button type="submit" class="ft-btn ft-btn--secondary" style="height:42px">Apply</button>
                    <?php if ($filtersActive): ?>
                        <a href="<?= ip_h($mkListUrl(['q' => '', 'sort' => 'newest', 'ex' => 'all', 'pp' => 12, 'page' => 1, 'edit' => null])) ?>" class="ft-btn ft-btn--ghost" style="height:42px;display:flex;align-items:center">Reset</a>
                    <?php endif; ?>
                </form>
            </section>

            <?php if ($totalPages > 1): ?>
                <nav class="ft-surface" style="padding:0.75rem 1rem;border-radius:var(--radius-lg);border:1px solid var(--border);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px" aria-label="Routine pages">
                    <span class="ft-muted" style="font-size:0.85rem">Page <?= (int) $page ?> of <?= (int) $totalPages ?></span>
                    <div class="ft-flex" style="gap:8px;flex-wrap:wrap">
                        <?php if ($page > 1): ?>
                            <a class="ft-btn ft-btn--ghost" style="padding:6px 12px;font-size:0.85rem" href="<?= ip_h(ip_url('pages/' . $mkListUrl(['page' => $page - 1]))) ?>"><i class="fas fa-chevron-left"></i> Prev</a>
                        <?php endif; ?>
                        <?php if ($page < $totalPages): ?>
                            <a class="ft-btn ft-btn--ghost" style="padding:6px 12px;font-size:0.85rem" href="<?= ip_h(ip_url('pages/' . $mkListUrl(['page' => $page + 1]))) ?>">Next <i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                </nav>
            <?php endif; ?>

            <!-- Routines List -->
            <div class="dash-programs" style="margin-top:0;display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:var(--space-5)">
                <?php foreach ($routines as $r): ?>
                    <?php
                    $exs = $routineExercises[(int) $r['id']] ?? [];
                    $totalSets = 0;
                    foreach ($exs as $row) {
                        $totalSets += (int) $row['sets'];
                    }
                    $updatedRaw = (string) ($r['updated_at'] ?? $r['created_at'] ?? '');
                    $updatedLabel = $updatedRaw !== '' ? date('M j, Y g:i A', strtotime($updatedRaw)) : '';
                    ?>
                    <article class="program-tile" style="position:relative;border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;background:var(--surface);display:flex;flex-direction:column">
                        <div class="bg" style="background-image:url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=400&q=70');height:80px"></div>
                        <div class="scrim" style="background:linear-gradient(to bottom, rgba(0,0,0,0.1), var(--surface));height:80px"></div>

                        <div class="body" style="padding:1rem;position:relative;z-index:10;flex:1">
                            <h3 style="margin:0 0 0.5rem 0;font-size:1.1rem;color:var(--text)"><?= ip_h((string) $r['title']) ?></h3>
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:0.75rem;flex-wrap:wrap">
                                <span style="background:var(--bg);padding:2px 8px;border-radius:12px;font-size:0.75rem;color:var(--text-muted);font-weight:600">
                                    <i class="fas fa-dumbbell" style="margin-right:4px;font-size:0.7rem"></i><?= (int) $r['exercise_count'] ?> exercises
                                </span>
                                <?php if ($totalSets > 0): ?>
                                    <span style="background:var(--bg);padding:2px 8px;border-radius:12px;font-size:0.75rem;color:var(--text-muted);font-weight:600" title="Sum of set counts across the routine">
                                        <i class="fas fa-layer-group" style="margin-right:4px;font-size:0.7rem"></i><?= (int) $totalSets ?> sets
                                    </span>
                                <?php endif; ?>
                                <?php if ($updatedLabel !== ''): ?>
                                    <span style="background:var(--bg);padding:2px 8px;border-radius:12px;font-size:0.75rem;color:var(--text-muted);font-weight:600">
                                        <i class="fas fa-clock" style="margin-right:4px;font-size:0.7rem"></i><?= ip_h($updatedLabel) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if ($r['description']): ?>
                                <p style="margin:0 0 1rem 0;font-size:0.85rem;color:var(--text-muted);line-height:1.4">
                                    <?= ip_h(mb_substr((string) $r['description'], 0, 100)) ?><?= strlen((string) $r['description']) > 100 ? '...' : '' ?>
                                </p>
                            <?php endif; ?>

                            <!-- Exercise Preview -->
                            <?php if ($exs !== []): ?>
                                <details style="background:var(--bg);border-radius:var(--radius-md);padding:0.75rem;font-size:0.8rem;border:1px solid var(--border)">
                                    <summary style="cursor:pointer;font-weight:600;color:var(--text);list-style:none;display:flex;align-items:center;gap:8px">
                                        <i class="fas fa-chevron-down" style="font-size:0.7rem;transition:transform 0.15s"></i>
                                        Exercise list (<?= count($exs) ?>)
                                    </summary>
                                    <ul style="margin:0.75rem 0 0;padding-left:1.2rem;color:var(--text-muted)">
                                        <?php foreach ($exs as $ex): ?>
                                            <li style="margin-bottom:4px">
                                                <strong><?= ip_h((string) $ex['name']) ?></strong>
                                                <span style="opacity:0.7">(<?= (int) $ex['sets'] ?>×<?= (int) $ex['reps'] ?>)</span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            <?php else: ?>
                                <div style="background:var(--bg);border-radius:var(--radius-md);padding:0.75rem;font-size:0.8rem;color:var(--text-muted);text-align:center;border:1px dashed var(--border)">
                                    No exercises added yet.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Actions Footer -->
                        <div style="padding:0.75rem 1rem;background:var(--bg);border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap">
                            <form method="post" action="<?= ip_h(ip_url('actions/duplicate_routine.php')) ?>" style="display:inline">
                                <?= ip_csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <button type="submit" class="ft-btn ft-btn--ghost" style="padding:4px 12px;font-size:0.8rem" title="Duplicate routine"><i class="fas fa-copy"></i> Duplicate</button>
                            </form>
                            <a href="<?= ip_h(ip_url('pages/' . $mkListUrl(['edit' => (int) $r['id'], 'page' => 1]))) ?>" class="ft-btn ft-btn--ghost" style="padding:4px 12px;font-size:0.8rem" title="Edit routine"><i class="fas fa-edit"></i> Edit</a>
                            <form method="post" action="<?= ip_h(ip_url('actions/delete_routine.php')) ?>" style="display:inline" onsubmit="return confirm('Delete this routine? All associated exercises will be unlinked.');">
                                <?= ip_csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <button type="submit" class="ft-btn ft-btn--ghost" style="padding:4px 12px;font-size:0.8rem;color:var(--danger)" title="Delete routine"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if (!$routines): ?>
                    <div style="grid-column:1/-1;padding:4rem 2rem;text-align:center;background:var(--surface);border:1px dashed var(--border);border-radius:var(--radius-lg)">
                        <div style="font-size:2.5rem;margin-bottom:1rem;color:var(--text-muted)">📋</div>
                        <h3 style="margin:0 0 0.5rem 0">No routines found</h3>
                        <p class="ft-muted" style="margin:0">Try adjusting search or filters, or create a new routine.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add/Edit Form Sidebar -->
        <div style="display:grid;gap:var(--space-6)">
            <?php if ($editRow): ?>
                <section class="ft-surface" style="padding:var(--space-5);border-radius:var(--radius-lg);border:1px solid var(--border);position:sticky;top:20px">
                    <h2 style="margin-top:0;font-size:1.1rem">Edit routine</h2>
                    <form method="post" action="<?= ip_h(ip_url('actions/edit_routine.php')) ?>">
                        <?= ip_csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $editRow['id'] ?>">
                        <div style="display:grid;gap:1rem">
                            <div>
                                <label class="ft-label">Title</label>
                                <input class="ft-input" style="width:100%" name="title" required value="<?= ip_h((string) $editRow['title']) ?>">
                            </div>
                            <div>
                                <label class="ft-label">Description</label>
                                <textarea class="ft-input" style="width:100%;min-height:120px" name="description" placeholder="Describe the focus or schedule for this routine..."><?= ip_h((string) ($editRow['description'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        <div style="margin-top:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:10px">
                            <button type="submit" class="ft-btn ft-btn--primary">Save changes</button>
                            <a class="ft-btn ft-btn--secondary" style="text-align:center" href="<?= ip_h(ip_url('pages/' . $mkListUrl(['edit' => null]))) ?>">Cancel</a>
                        </div>
                    </form>
                </section>
            <?php else: ?>
                <section class="ft-surface" style="padding:var(--space-5);border-radius:var(--radius-lg);border:1px solid var(--border);position:sticky;top:20px">
                    <h2 style="margin-top:0;font-size:1.1rem">Create routine</h2>
                    <form method="post" action="<?= ip_h(ip_url('actions/add_routine.php')) ?>">
                        <?= ip_csrf_field() ?>
                        <div style="display:grid;gap:1rem">
                            <div>
                                <label class="ft-label">Title</label>
                                <input class="ft-input" style="width:100%" name="title" required placeholder="e.g., Push/Pull/Legs">
                            </div>
                            <div>
                                <label class="ft-label">Description</label>
                                <textarea class="ft-input" style="width:100%;min-height:120px" name="description" placeholder="Describe the focus or schedule for this routine..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="ft-btn ft-btn--primary" style="margin-top:1.5rem;width:100%">Create routine</button>
                    </form>
                </section>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
