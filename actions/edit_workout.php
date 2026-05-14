<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/workouts.php');
}

$id = (int) ($_POST['id'] ?? 0);
$title = trim((string) ($_POST['title'] ?? ''));
$category = trim((string) ($_POST['category'] ?? 'General')) ?: 'General';
$duration = max(0, (int) ($_POST['duration'] ?? 0));
$calories = max(0, (int) ($_POST['calories'] ?? 0));
$workout_date = trim((string) ($_POST['workout_date'] ?? ''));
$notes = trim((string) ($_POST['notes'] ?? ''));

if ($id < 1 || $title === '') {
    ip_flash_set('error', 'Invalid workout.');
    ip_redirect('pages/workouts.php');
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $workout_date)) {
    $workout_date = date('Y-m-d');
}

$up = $pdo->prepare(
    'UPDATE workouts SET title=?, category=?, duration=?, calories=?, workout_date=?, notes=?, updated_at=CURRENT_TIMESTAMP
     WHERE id=? AND user_id=?',
);
$up->execute([
    mb_substr($title, 0, 200),
    mb_substr($category, 0, 80),
    $duration,
    $calories,
    $workout_date,
    mb_substr($notes, 0, 5000),
    $id,
    $userId,
]);

ip_flash_set('success', $up->rowCount() ? 'Workout updated.' : 'No changes.');
ip_redirect('pages/workouts.php?edit=' . $id);
