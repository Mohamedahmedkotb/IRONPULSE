<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/utils/Response.php';

function require_auth(): int
{
    $uid = $_SESSION['user_id'] ?? null;
    if (!is_int($uid) && !is_string($uid)) {
        Response::error('Unauthorized', 401);
    }
    return (int) $uid;
}
