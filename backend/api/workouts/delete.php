<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

$m = Request::method();
if ($m !== 'DELETE' && $m !== 'POST') {
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
if (!WorkoutRepository::delete($pdo, $id, $uid)) {
    Response::error('Not found', 404);
}
Response::ok('Workout deleted', null);
