<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/exercises.php');
}

$routine_id = (int) ($_POST['routine_id'] ?? 0);
$exercise_id = (int) ($_POST['exercise_id'] ?? 0);
$sets = (int) ($_POST['sets'] ?? 3);
$reps = (int) ($_POST['reps'] ?? 10);
$redirect_to = trim((string) ($_POST['redirect_to'] ?? 'pages/exercises.php'));

if ($routine_id < 1 || $exercise_id < 1) {
    ip_flash_set('error', 'Invalid routine or exercise.');
    ip_redirect($redirect_to);
}

// Ensure user owns routine
$checkRoutine = $pdo->prepare('SELECT id FROM routines WHERE id = ? AND user_id = ?');
$checkRoutine->execute([$routine_id, $userId]);
if (!$checkRoutine->fetchColumn()) {
    ip_flash_set('error', 'Routine not found.');
    ip_redirect($redirect_to);
}

try {
    // Get max sort_order
    $orderSt = $pdo->prepare('SELECT MAX(sort_order) FROM routine_exercises WHERE routine_id = ?');
    $orderSt->execute([$routine_id]);
    $maxOrder = (int) $orderSt->fetchColumn();

    $ins = $pdo->prepare('INSERT INTO routine_exercises (routine_id, exercise_id, sets, reps, sort_order) VALUES (?, ?, ?, ?, ?)');
    $ins->execute([$routine_id, $exercise_id, $sets, $reps, $maxOrder + 1]);
    
    ip_flash_set('success', 'Exercise added to routine.');
} catch (PDOException $e) {
    ip_flash_set('error', 'Could not add exercise to routine.');
}

ip_redirect($redirect_to);
