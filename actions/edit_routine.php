<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/routines.php');
}

$id = (int) ($_POST['id'] ?? 0);
$title = trim((string) ($_POST['title'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));

if ($id < 1 || $title === '') {
    ip_flash_set('error', 'Invalid routine data.');
    ip_redirect('pages/routines.php');
}

$up = $pdo->prepare('UPDATE routines SET title=?, description=?, updated_at=CURRENT_TIMESTAMP WHERE id=? AND user_id=?');
$up->execute([mb_substr($title, 0, 200), mb_substr($description, 0, 5000), $id, $userId]);

ip_flash_set('success', $up->rowCount() ? 'Routine updated.' : 'No changes made.');
ip_redirect('pages/routines.php');
