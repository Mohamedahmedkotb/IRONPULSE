<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/routines.php');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $del = $pdo->prepare('DELETE FROM routines WHERE id = ? AND user_id = ?');
    $del->execute([$id, $userId]);
    if ($del->rowCount() > 0) {
        ip_flash_set('success', 'Routine deleted.');
    } else {
        ip_flash_set('error', 'Could not delete routine.');
    }
}

ip_redirect('pages/routines.php');
