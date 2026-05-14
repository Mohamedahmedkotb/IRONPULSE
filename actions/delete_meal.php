<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/meals.php');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $del = $pdo->prepare('DELETE FROM meal_plans WHERE id = ? AND user_id = ?');
    $del->execute([$id, $userId]);
    if ($del->rowCount() > 0) {
        ip_flash_set('success', 'Meal plan deleted.');
    } else {
        ip_flash_set('error', 'Could not delete meal plan.');
    }
}

ip_redirect('pages/meals.php');
