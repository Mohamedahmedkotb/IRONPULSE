<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_flash_set('error', 'Invalid request.');
    ip_redirect('pages/routines.php');
}

$title = trim((string) ($_POST['title'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));

if ($title === '') {
    ip_flash_set('error', 'Routine title is required.');
    ip_redirect('pages/routines.php');
}

try {
    $ins = $pdo->prepare('INSERT INTO routines (user_id, title, description) VALUES (?, ?, ?)');
    $ins->execute([$userId, mb_substr($title, 0, 200), mb_substr($description, 0, 5000)]);
    ip_flash_set('success', 'Routine created successfully.');
} catch (PDOException $e) {
    ip_flash_set('error', 'Could not save the routine. Please try again.');
}

ip_redirect('pages/routines.php');
