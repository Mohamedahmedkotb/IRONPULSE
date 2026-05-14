<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/workouts.php');
}

$weId = (int) ($_POST['we_id'] ?? 0);
$workoutId = (int) ($_POST['workout_id'] ?? 0);

if ($weId < 1 || $workoutId < 1) {
    ip_flash_set('error', 'Invalid entry.');
    ip_redirect('pages/workouts.php');
}

$del = $pdo->prepare(
    'DELETE we FROM workout_exercises we
     INNER JOIN workouts w ON w.id = we.workout_id
     WHERE we.id = ? AND we.workout_id = ? AND w.user_id = ?',
);
$del->execute([$weId, $workoutId, $userId]);

ip_flash_set('success', $del->rowCount() ? 'Exercise removed from workout.' : 'Nothing to remove.');
ip_redirect('pages/workouts.php?edit=' . $workoutId);
