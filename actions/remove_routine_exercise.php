<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/routines.php');
}

$routine_id = (int) ($_POST['routine_id'] ?? 0);
$re_id = (int) ($_POST['re_id'] ?? 0);

if ($routine_id > 0 && $re_id > 0) {
    // Verify ownership
    $check = $pdo->prepare('SELECT id FROM routines WHERE id = ? AND user_id = ?');
    $check->execute([$routine_id, $userId]);
    if ($check->fetchColumn()) {
        $del = $pdo->prepare('DELETE FROM routine_exercises WHERE id = ? AND routine_id = ?');
        $del->execute([$re_id, $routine_id]);
        ip_flash_set('success', 'Exercise removed from routine.');
    }
}

ip_redirect('pages/routines.php?edit=' . $routine_id);
