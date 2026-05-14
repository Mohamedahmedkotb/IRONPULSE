<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/routines.php');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id < 1) {
    ip_flash_set('error', 'Invalid routine.');
    ip_redirect('pages/routines.php');
}

try {
    $pdo->beginTransaction();

    // Fetch original routine
    $st = $pdo->prepare('SELECT title, description FROM routines WHERE id = ? AND user_id = ?');
    $st->execute([$id, $userId]);
    $orig = $st->fetch();

    if (!$orig) {
        throw new Exception("Routine not found or access denied.");
    }

    // Insert new routine
    $newTitle = $orig['title'] . ' (Copy)';
    $ins = $pdo->prepare('INSERT INTO routines (user_id, title, description) VALUES (?, ?, ?)');
    $ins->execute([$userId, $newTitle, $orig['description']]);
    $newRoutineId = (int) $pdo->lastInsertId();

    // Copy exercises
    $exSt = $pdo->prepare('SELECT exercise_id, sort_order, sets, reps, weight_kg, rest_seconds FROM routine_exercises WHERE routine_id = ?');
    $exSt->execute([$id]);
    $exercises = $exSt->fetchAll();

    if ($exercises) {
        $insEx = $pdo->prepare('INSERT INTO routine_exercises (routine_id, exercise_id, sort_order, sets, reps, weight_kg, rest_seconds) VALUES (?, ?, ?, ?, ?, ?, ?)');
        foreach ($exercises as $ex) {
            $insEx->execute([
                $newRoutineId,
                $ex['exercise_id'],
                $ex['sort_order'],
                $ex['sets'],
                $ex['reps'],
                $ex['weight_kg'],
                $ex['rest_seconds']
            ]);
        }
    }

    $pdo->commit();
    ip_flash_set('success', 'Routine duplicated successfully.');

} catch (Exception $e) {
    $pdo->rollBack();
    ip_flash_set('error', 'Failed to duplicate routine.');
}

ip_redirect('pages/routines.php');
