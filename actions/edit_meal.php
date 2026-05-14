<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/meals.php');
}

$id = (int) ($_POST['id'] ?? 0);
$title = trim((string) ($_POST['title'] ?? ''));
$plan_date = trim((string) ($_POST['plan_date'] ?? ''));
$breakfast = trim((string) ($_POST['breakfast'] ?? ''));
$lunch = trim((string) ($_POST['lunch'] ?? ''));
$dinner = trim((string) ($_POST['dinner'] ?? ''));
$calories = (int) ($_POST['calories'] ?? 0);
$protein_g = (int) ($_POST['protein_g'] ?? 0);
$carbs_g = (int) ($_POST['carbs_g'] ?? 0);
$fats_g = (int) ($_POST['fats_g'] ?? 0);

if ($id < 1 || $title === '' || $plan_date === '') {
    ip_flash_set('error', 'Invalid meal plan data.');
    ip_redirect('pages/meals.php');
}

try {
    $up = $pdo->prepare('UPDATE meal_plans SET plan_date=?, title=?, breakfast=?, lunch=?, dinner=?, calories=?, protein_g=?, carbs_g=?, fats_g=?, updated_at=CURRENT_TIMESTAMP WHERE id=? AND user_id=?');
    $up->execute([$plan_date, mb_substr($title, 0, 200), mb_substr($breakfast, 0, 255), mb_substr($lunch, 0, 255), mb_substr($dinner, 0, 255), $calories, $protein_g, $carbs_g, $fats_g, $id, $userId]);
    ip_flash_set('success', $up->rowCount() ? 'Meal plan updated.' : 'No changes made.');
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        ip_flash_set('error', 'A meal plan already exists for this date.');
    } else {
        ip_flash_set('error', 'Could not update the meal plan.');
    }
}

ip_redirect('pages/meals.php');
