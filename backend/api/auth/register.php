<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'POST') {
    Response::error('Method not allowed', 405);
}

ironpulse_require_csrf_except(['register.php', 'login.php']);

$body = Request::jsonBody();
$pdo = Database::pdo();
$out = AuthService::register($pdo, [
    'full_name' => (string) ($body['full_name'] ?? $body['name'] ?? ''),
    'email' => (string) ($body['email'] ?? ''),
    'password' => (string) ($body['password'] ?? ''),
    'fitness_goal' => (string) ($body['fitness_goal'] ?? $body['goal'] ?? ''),
    'gender' => (string) ($body['gender'] ?? ''),
]);
Response::ok('Account created', $out);
