<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'GET') {
    Response::error('Method not allowed', 405);
}

require_once dirname(__DIR__, 2) . '/middleware/require_auth.php';
$uid = require_auth();
$pdo = Database::pdo();
$user = UserRepository::findById($pdo, $uid);
if (!$user) {
    Response::error('User not found', 404);
}

$dates = WorkoutRepository::distinctWorkoutDates($pdo, $uid);
$streak = ironpulse_compute_streak($dates);
$pdo->prepare('UPDATE users SET streak = ? WHERE id = ?')->execute([$streak, $uid]);
$user['streak'] = $streak;

$workoutCount = WorkoutRepository::countByUser($pdo, $uid);
$routinesCount = RoutineRepository::countByUser($pdo, $uid);
$calWeek = WorkoutRepository::sumCaloriesLastDays($pdo, $uid, 7);
$chart = WorkoutRepository::lastSessionsCalories($pdo, $uid, 7);
if (count($chart) < 7) {
    $pad = array_fill(0, 7 - count($chart), 0);
    $chart = array_merge($pad, $chart);
}

$token = Csrf::token();
$public = ironpulse_user_public($user);

Response::ok('OK', [
    'user' => $public,
    'csrf_token' => $token,
    'stats' => [
        'workout_count' => $workoutCount,
        'routines_count' => $routinesCount,
        'calories_week' => $calWeek,
        'streak_days' => $streak,
        'chart_calories_last_7' => $chart,
    ],
]);
