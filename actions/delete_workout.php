<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/workouts.php');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id < 1) {
    ip_redirect('pages/workouts.php');
}

$del = $pdo->prepare('DELETE FROM workouts WHERE id = ? AND user_id = ?');
$del->execute([$id, $userId]);
ip_flash_set('success', $del->rowCount() ? 'Workout deleted.' : 'Nothing to delete.');
ip_redirect('pages/workouts.php');
