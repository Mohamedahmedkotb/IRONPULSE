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

$goals = [];
if (($user['fitness_goal'] ?? '') !== '') {
    $goals = array_map('trim', explode(',', (string) $user['fitness_goal']));
}

Response::ok('OK', [
    'profile' => [
        'name' => $user['full_name'],
        'email' => $user['email'],
        'phone' => $user['phone'] ?? '',
        'city' => $user['city'] ?? '',
        'gender' => $user['gender'] ?? '',
        'bio' => $user['bio'] ?? '',
        'goals' => $goals,
        'age' => $user['age'] !== null ? (int) $user['age'] : null,
        'height' => $user['height'] !== null ? (float) $user['height'] : null,
        'weight' => $user['weight'] !== null ? (float) $user['weight'] : null,
        'activity_level' => $user['activity_level'] ?? '',
        'avatar' => $user['avatar'] ?? '',
        'xp' => (int) ($user['xp'] ?? 0),
        'streak' => (int) ($user['streak'] ?? 0),
    ],
]);
