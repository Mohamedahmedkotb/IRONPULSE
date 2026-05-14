<?php

declare(strict_types=1);

function ip_csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function ip_csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . ip_h(ip_csrf_token()) . '">';
}

function ip_csrf_validate(): bool
{
    $sent = $_POST['_csrf'] ?? '';
    return is_string($sent) && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $sent);
}
