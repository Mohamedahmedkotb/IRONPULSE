<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'GET') {
    Response::error('Method not allowed', 405);
}

require_once dirname(__DIR__, 2) . '/middleware/require_auth.php';
$uid = require_auth();
$list = WorkoutRepository::listByUser(Database::pdo(), $uid);
$mapped = array_map(static function (array $w) {
    return [
        'id' => (string) $w['id'],
        'name' => $w['title'],
        'type' => $w['category'],
        'date' => $w['workout_date'],
        'durationMin' => (int) $w['duration'],
        'calories' => (int) $w['calories'],
        'notes' => $w['notes'] ?? '',
    ];
}, $list);
Response::ok('OK', ['workouts' => $mapped]);
