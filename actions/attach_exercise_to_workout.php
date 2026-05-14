<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_flash_set('error', 'Invalid request.');
    ip_redirect('pages/exercises.php');
}

$workoutId = (int) ($_POST['workout_id'] ?? 0);
$exerciseId = (int) ($_POST['exercise_id'] ?? 0);
$sets = max(1, min(99, (int) ($_POST['sets'] ?? 3)));
$reps = max(1, min(999, (int) ($_POST['reps'] ?? 10)));

if ($workoutId < 1 || $exerciseId < 1) {
    ip_flash_set('error', 'Choose a workout and exercise.');
    ip_redirect('pages/exercises.php');
}

$w = $pdo->prepare('SELECT id FROM workouts WHERE id = ? AND user_id = ? LIMIT 1');
$w->execute([$workoutId, $userId]);
if (!$w->fetch()) {
    ip_flash_set('error', 'Workout not found.');
    ip_redirect('pages/exercises.php');
}

$ex = $pdo->prepare('SELECT id FROM exercises WHERE id = ? LIMIT 1');
$ex->execute([$exerciseId]);
if (!$ex->fetch()) {
    ip_flash_set('error', 'Exercise not found.');
    ip_redirect('pages/exercises.php');
}

$mx = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) + 1 FROM workout_exercises WHERE workout_id = ?');
$mx->execute([$workoutId]);
$sort = (int) $mx->fetchColumn();

try {
    $ins = $pdo->prepare(
        'INSERT INTO workout_exercises (workout_id, exercise_id, sort_order, sets, reps)
         VALUES (?, ?, ?, ?, ?)',
    );
    $ins->execute([$workoutId, $exerciseId, $sort, $sets, $reps]);
} catch (PDOException $e) {
    if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate')) {
        ip_flash_set('error', 'That exercise is already on this workout.');
    } else {
        ip_flash_set('error', 'Could not attach exercise. If you just upgraded, run database/migrate_add_workout_exercises.sql.');
    }
    ip_redirect('pages/workouts.php?edit=' . $workoutId);
}

ip_flash_set('success', 'Exercise added to your workout.');
ip_redirect('pages/workouts.php?edit=' . $workoutId);
