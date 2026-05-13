<?php

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validate(?string $sent): bool
    {
        if ($sent === null || $sent === '') {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'] ?? '', $sent);
    }

    /** Call on mutating requests (POST/PUT/DELETE) except auth/register/login */
    public static function requireValid(): void
    {
        $cfg = require dirname(__DIR__) . '/config/config.php';
        $header = $cfg['csrf_header'] ?? 'X-CSRF-Token';
        $sent = Request::header($header) ?? ($_POST['_csrf'] ?? null);
        if (!self::validate(is_string($sent) ? $sent : null)) {
            Response::error('Invalid or missing CSRF token', 419);
        }
    }
}
