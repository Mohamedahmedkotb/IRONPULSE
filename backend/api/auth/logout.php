<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'POST') {
    Response::error('Method not allowed', 405);
}

ironpulse_require_csrf_except(['register.php', 'login.php']);

require_once dirname(__DIR__, 2) . '/middleware/require_auth.php';
require_auth();
AuthService::logout();
Response::ok('Logged out', null);
