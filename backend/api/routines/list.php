<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'GET') {
    Response::error('Method not allowed', 405);
}

require_once dirname(__DIR__, 2) . '/middleware/require_auth.php';
$uid = require_auth();
$pdo = Database::pdo();
$list = RoutineRepository::listByUser($pdo, $uid);
$out = [];
foreach ($list as $r) {
    $ex = RoutineRepository::exercisesForRoutine($pdo, (int) $r['id']);
    $out[] = [
        'id' => (string) $r['id'],
        'title' => $r['title'],
        'description' => $r['description'] ?? '',
        'exercises' => array_map(static function ($e) {
            return [
                'name' => $e['name'],
                'sets' => (int) $e['sets'],
                'reps' => (int) $e['reps'],
                'exercise_id' => (int) $e['exercise_id'],
            ];
        }, $ex),
    ];
}
Response::ok('OK', ['routines' => $out]);
