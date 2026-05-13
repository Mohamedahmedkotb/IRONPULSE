<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'POST') {
    Response::error('Method not allowed', 405);
}

ironpulse_require_csrf_except(['register.php', 'login.php']);

require_once dirname(__DIR__, 2) . '/middleware/require_auth.php';
$uid = require_auth();
$body = Request::jsonBody();
$planDate = substr(Sanitizer::string((string) ($body['plan_date'] ?? $body['date'] ?? date('Y-m-d')), 10), 0, 10);
MealRepository::upsertDay(Database::pdo(), $uid, [
    'plan_date' => $planDate,
    'title' => Sanitizer::string((string) ($body['title'] ?? ''), 200),
    'breakfast' => Sanitizer::string((string) ($body['breakfast'] ?? ''), 255),
    'lunch' => Sanitizer::string((string) ($body['lunch'] ?? ''), 255),
    'dinner' => Sanitizer::string((string) ($body['dinner'] ?? ''), 255),
    'calories' => (int) ($body['calories'] ?? 0),
    'protein_g' => (int) ($body['protein_g'] ?? $body['protein'] ?? 0),
    'carbs_g' => (int) ($body['carbs_g'] ?? $body['carbs'] ?? 0),
    'fats_g' => (int) ($body['fats_g'] ?? $body['fats'] ?? 0),
]);
Response::ok('Meal plan saved', null);
