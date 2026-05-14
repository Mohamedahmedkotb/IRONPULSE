<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ip_redirect('pages/signup.php');
}

if (!ip_csrf_validate()) {
    ip_flash_set('error', 'Invalid security token.');
    ip_redirect('pages/signup.php');
}

$full = trim((string) ($_POST['full_name'] ?? ''));
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
$gender = trim((string) ($_POST['gender'] ?? ''));
$goal = trim((string) ($_POST['fitness_goal'] ?? ''));

if (strlen($full) < 2) {
    ip_flash_set('error', 'Please enter your full name.');
    ip_redirect('pages/signup.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ip_flash_set('error', 'Invalid email address.');
    ip_redirect('pages/signup.php');
}

$chk = ip_password_ok($password);
if (!$chk['ok']) {
    ip_flash_set('error', 'Password must be at least 8 characters with mixed character types.');
    ip_redirect('pages/signup.php');
}

$st = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$st->execute([$email]);
if ($st->fetch()) {
    ip_flash_set('error', 'That email is already registered.');
    ip_redirect('pages/signup.php');
}

$username = ip_unique_username($pdo, strstr($email, '@', true) ?: 'user');
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $ins = $pdo->prepare(
        'INSERT INTO users (username, full_name, email, password_hash, gender, fitness_goal, role)
         VALUES (?, ?, ?, ?, ?, ?, ?)',
    );
    $ins->execute([$username, $full, $email, $hash, mb_substr($gender, 0, 32), mb_substr($goal, 0, 120), 'member']);
    $uid = (int) $pdo->lastInsertId();
} catch (PDOException $e) {
    $msg = $e->getMessage();
    if (str_contains($msg, '1062') || str_contains($msg, 'Duplicate') || (string) $e->getCode() === '23000') {
        ip_flash_set('error', 'That email is already registered.');
    } else {
        ip_flash_set('error', 'Could not create your account. Check the database is set up and try again.');
    }
    ip_redirect('pages/signup.php');
}

if ($uid < 1) {
    ip_flash_set('error', 'Could not create your account. Please try again.');
    ip_redirect('pages/signup.php');
}

session_regenerate_id(true);
$_SESSION['user_id'] = $uid;
ip_flash_set('success', 'Welcome! You are signed in.');
ip_redirect('pages/dashboard.php');
