<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ip_redirect('pages/login.php');
}

if (!ip_csrf_validate()) {
    ip_flash_set('error', 'Invalid security token.');
    ip_redirect('pages/login.php');
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    ip_flash_set('error', 'Email and password are required.');
    ip_redirect('pages/login.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ip_flash_set('error', 'Invalid email.');
    ip_redirect('pages/login.php');
}

$st = $pdo->prepare('SELECT id, email, password_hash FROM users WHERE email = ? LIMIT 1');
$st->execute([$email]);
$row = $st->fetch();
if (!$row || !password_verify($password, $row['password_hash'])) {
    ip_flash_set('error', 'Invalid credentials.');
    ip_redirect('pages/login.php');
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $row['id'];
ip_flash_set('success', 'Welcome back.');
ip_redirect('pages/dashboard.php');
