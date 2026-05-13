<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'PUT' && Request::method() !== 'POST') {
    Response::error('Method not allowed', 405);
}

ironpulse_require_csrf_except(['register.php', 'login.php']);

require_once dirname(__DIR__, 2) . '/middleware/require_auth.php';
$uid = require_auth();
$body = Request::jsonBody();
$id = (int) ($body['id'] ?? $_GET['id'] ?? 0);
if ($id <= 0) {
    Response::error('Invalid id', 422);
}

$pdo = Database::pdo();
if (!WorkoutRepository::find($pdo, $id, $uid)) {
    Response::error('Not found', 404);
}

$d = [
    'title' => Sanitizer::string((string) ($body['name'] ?? $body['title'] ?? ''), 200),
    'category' => Sanitizer::string((string) ($body['type'] ?? $body['category'] ?? 'General'), 80),
    'duration' => (int) ($body['durationMin'] ?? $body['duration'] ?? 0),
    'calories' => (int) ($body['calories'] ?? 0),
    'workout_date' => substr(Sanitizer::string((string) ($body['date'] ?? date('Y-m-d')), 10), 0, 10),
    'notes' => Sanitizer::string((string) ($body['notes'] ?? ''), 4000),
];
WorkoutRepository::update($pdo, $id, $uid, $d);
Response::ok('Workout updated', ['id' => (string) $id]);
