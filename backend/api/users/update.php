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
$pdo = Database::pdo();

$goals = $body['goals'] ?? null;
$fitnessGoalStr = null;
if (is_array($goals)) {
    $fitnessGoalStr = implode(',', array_map(static fn ($g) => Sanitizer::string((string) $g, 80), $goals));
}

$patch = [];
if (isset($body['full_name']) || isset($body['name'])) {
    $patch['full_name'] = Sanitizer::string((string) ($body['full_name'] ?? $body['name'] ?? ''), 120);
}
if (isset($body['email'])) {
    $em = Sanitizer::email((string) $body['email']);
    if (Validator::email($em)) {
        $other = UserRepository::findByEmail($pdo, $em);
        if ($other && (int) $other['id'] !== $uid) {
            Response::error('Email already in use', 409);
        }
        $patch['email'] = $em;
    }
}
if (isset($body['phone'])) {
    $patch['phone'] = Sanitizer::string((string) $body['phone'], 40);
}
if (isset($body['city'])) {
    $patch['city'] = Sanitizer::string((string) $body['city'], 120);
}
if (isset($body['gender'])) {
    $patch['gender'] = Sanitizer::string((string) $body['gender'], 32);
}
if (isset($body['bio'])) {
    $patch['bio'] = Sanitizer::string((string) $body['bio'], 2000);
}
if (isset($body['activity_level'])) {
    $patch['activity_level'] = Sanitizer::string((string) $body['activity_level'], 64);
}
if ($fitnessGoalStr !== null) {
    $patch['fitness_goal'] = $fitnessGoalStr;
}
if (array_key_exists('age', $body)) {
    $patch['age'] = $body['age'] === null || $body['age'] === '' ? null : (int) $body['age'];
}
if (array_key_exists('height', $body)) {
    $patch['height'] = $body['height'] === null || $body['height'] === '' ? null : (float) $body['height'];
}
if (array_key_exists('weight', $body)) {
    $patch['weight'] = $body['weight'] === null || $body['weight'] === '' ? null : (float) $body['weight'];
}

if ($patch === []) {
    Response::ok('Nothing to update', null);
}

UserRepository::update($pdo, $uid, $patch);
$user = UserRepository::findById($pdo, $uid);
Response::ok('Profile updated', ['user' => ironpulse_user_public($user)]);
