<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'GET') {
    Response::error('Method not allowed', 405);
}

require_once dirname(__DIR__, 2) . '/middleware/require_auth.php';
$uid = require_auth();
$list = ProgressRepository::listByUser(Database::pdo(), $uid, 120);
Response::ok('OK', ['logs' => $list]);
