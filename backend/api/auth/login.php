<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'POST') {
    Response::error('Method not allowed', 405);
}

ironpulse_require_csrf_except(['register.php', 'login.php']);

$body = Request::jsonBody();
$email = Sanitizer::email((string) ($body['email'] ?? ''));
$password = (string) ($body['password'] ?? '');
if ($email === '' || $password === '') {
    Response::error('Email and password required', 422);
}

$pdo = Database::pdo();
$out = AuthService::login($pdo, $email, $password);
Response::ok('Welcome back', $out);
