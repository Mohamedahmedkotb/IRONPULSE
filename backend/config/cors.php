<?php

/**
 * Apply CORS headers from allowed origins (comma-separated env or config).
 */
function ironpulse_apply_cors(): void
{
    $allowed = getenv('IRONPULSE_CORS_ORIGINS');
    if ($allowed === false || $allowed === '') {
        $allowed = 'http://localhost,http://127.0.0.1,http://localhost:5500,http://127.0.0.1:5500';
    }
    $list = array_map('trim', explode(',', $allowed));
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin !== '' && in_array($origin, $list, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Vary: Origin');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token, X-Requested-With');
        header('Access-Control-Max-Age: 86400');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
