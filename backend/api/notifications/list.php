<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'GET') {
    Response::error('Method not allowed', 405);
}

require_once dirname(__DIR__, 2) . '/middleware/require_auth.php';
$uid = require_auth();
$list = NotificationRepository::list(Database::pdo(), $uid, 100);
Response::ok('OK', ['notifications' => $list]);
