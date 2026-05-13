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
$nid = isset($body['id']) ? (int) $body['id'] : (int) ($_GET['id'] ?? 0);
$count = NotificationRepository::markRead(Database::pdo(), $uid, $nid > 0 ? $nid : null);
Response::ok('Updated', ['marked' => $count]);
