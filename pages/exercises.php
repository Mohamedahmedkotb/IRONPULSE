<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
ip_require_login();

$currentUser = ip_current_user($pdo);
if (!$currentUser) {
    session_destroy();
    ip_redirect('pages/login.php');
}

$st = $pdo->query(
    'SELECT id, name, category, muscle_group, difficulty, instructions FROM exercises ORDER BY name ASC LIMIT 200',
);
$exercises = $st->fetchAll();

$pageTitle = 'Exercise library';
$currentNav = 'exercises';

require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/sidebar.php';
require_once dirname(__DIR__) . '/includes/navbar.php';
?>
<main class="app-main">
    <h1 style="margin-top:0">Exercises</h1>
    <p class="ft-muted">Global library from MySQL (read-only).</p>
    <div class="ft-surface" style="margin-top:var(--space-5);border-radius:var(--radius-lg);border:1px solid var(--border);overflow:auto">
        <table style="width:100%;border-collapse:collapse;font-size:0.9rem;min-width:640px">
            <thead>
                <tr style="text-align:left;background:var(--page-bg);border-bottom:1px solid var(--border)">
                    <th style="padding:0.75rem 1rem">Name</th>
                    <th style="padding:0.75rem 1rem">Category</th>
                    <th style="padding:0.75rem 1rem">Muscle</th>
                    <th style="padding:0.75rem 1rem">Level</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exercises as $e): ?>
                    <tr style="border-bottom:1px solid var(--border)">
                        <td style="padding:0.6rem 1rem"><?= ip_h((string) $e['name']) ?></td>
                        <td style="padding:0.6rem 1rem"><?= ip_h((string) $e['category']) ?></td>
                        <td style="padding:0.6rem 1rem"><?= ip_h((string) $e['muscle_group']) ?></td>
                        <td style="padding:0.6rem 1rem"><?= ip_h((string) $e['difficulty']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
