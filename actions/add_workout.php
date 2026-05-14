<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_flash_set('error', 'Invalid request.');
    ip_redirect('pages/workouts.php');
}

$title = trim((string) ($_POST['title'] ?? ''));
$category = trim((string) ($_POST['category'] ?? 'General')) ?: 'General';
$duration = max(0, (int) ($_POST['duration'] ?? 0));
$calories = max(0, (int) ($_POST['calories'] ?? 0));
$workout_date = trim((string) ($_POST['workout_date'] ?? ''));
$notes = trim((string) ($_POST['notes'] ?? ''));

if ($title === '') {
    ip_flash_set('error', 'Workout title is required.');
    ip_redirect('pages/workouts.php');
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $workout_date)) {
    $workout_date = date('Y-m-d');
}

try {
    $ins = $pdo->prepare(
        'INSERT INTO workouts (user_id, title, category, duration, calories, workout_date, notes)
         VALUES (?, ?, ?, ?, ?, ?, ?)',
    );
    $ins->execute([$userId, mb_substr($title, 0, 200), mb_substr($category, 0, 80), $duration, $calories, $workout_date, mb_substr($notes, 0, 5000)]);
} catch (PDOException $e) {
    ip_flash_set('error', 'Could not save the workout. Please try again.');
    ip_redirect('pages/workouts.php');
}

$newId = (int) $pdo->lastInsertId();
ip_flash_set('success', 'Workout added. Attach exercises from the library or below.');
ip_redirect($newId > 0 ? 'pages/workouts.php?edit=' . $newId : 'pages/workouts.php');
