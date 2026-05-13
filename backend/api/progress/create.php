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
$logDate = substr(Sanitizer::string((string) ($body['log_date'] ?? $body['date'] ?? date('Y-m-d')), 10), 0, 10);
$id = ProgressRepository::create(Database::pdo(), $uid, [
    'weight' => isset($body['weight']) ? (float) $body['weight'] : null,
    'body_fat' => isset($body['body_fat']) ? (float) $body['body_fat'] : null,
    'bmi' => isset($body['bmi']) ? (float) $body['bmi'] : null,
    'log_date' => $logDate,
    'notes' => Sanitizer::string((string) ($body['notes'] ?? ''), 500),
]);
Response::ok('Progress logged', ['id' => (string) $id]);
