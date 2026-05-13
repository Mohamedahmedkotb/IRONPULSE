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
$pdo = Database::pdo();

$title = Sanitizer::string((string) ($body['title'] ?? ''), 200);
$description = Sanitizer::string((string) ($body['description'] ?? ''), 4000);
$exercises = $body['exercises'] ?? [];
if (!is_array($exercises) || $exercises === []) {
    Response::error('Add at least one exercise', 422);
}
if ($title === '') {
    $title = 'Untitled routine';
}

$norm = [];
foreach ($exercises as $ex) {
    if (!is_array($ex)) {
        continue;
    }
    $norm[] = [
        'exercise_id' => isset($ex['exercise_id']) ? (int) $ex['exercise_id'] : 0,
        'name' => (string) ($ex['name'] ?? ''),
        'sets' => max(1, (int) ($ex['sets'] ?? 3)),
        'reps' => max(1, (int) ($ex['reps'] ?? 10)),
    ];
}

$existingId = isset($body['id']) ? (int) $body['id'] : 0;
if ($existingId > 0 && RoutineRepository::find($pdo, $existingId, $uid)) {
    RoutineRepository::updateWithExercises($pdo, $existingId, $uid, $title, $description, $norm);
    Response::ok('Routine updated', ['id' => (string) $existingId]);
}

$rid = RoutineRepository::createWithExercises($pdo, $uid, $title, $description, $norm);
Response::ok('Routine created', ['id' => (string) $rid]);
