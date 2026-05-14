<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_flash_set('error', 'Invalid request.');
    ip_redirect('pages/meals.php');
}

$title = trim((string) ($_POST['title'] ?? ''));
$plan_date = trim((string) ($_POST['plan_date'] ?? ''));
$breakfast = trim((string) ($_POST['breakfast'] ?? ''));
$lunch = trim((string) ($_POST['lunch'] ?? ''));
$dinner = trim((string) ($_POST['dinner'] ?? ''));
$calories = (int) ($_POST['calories'] ?? 0);
$protein_g = (int) ($_POST['protein_g'] ?? 0);
$carbs_g = (int) ($_POST['carbs_g'] ?? 0);
$fats_g = (int) ($_POST['fats_g'] ?? 0);

if ($title === '' || $plan_date === '') {
    ip_flash_set('error', 'Title and Date are required.');
    ip_redirect('pages/meals.php');
}

try {
    $ins = $pdo->prepare('INSERT INTO meal_plans (user_id, plan_date, title, breakfast, lunch, dinner, calories, protein_g, carbs_g, fats_g) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $ins->execute([$userId, $plan_date, mb_substr($title, 0, 200), mb_substr($breakfast, 0, 255), mb_substr($lunch, 0, 255), mb_substr($dinner, 0, 255), $calories, $protein_g, $carbs_g, $fats_g]);
    ip_flash_set('success', 'Meal plan logged successfully.');
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        ip_flash_set('error', 'You already have a meal plan logged for this date.');
    } else {
        ip_flash_set('error', 'Could not save the meal plan. Please try again.');
    }
}

ip_redirect('pages/meals.php');
