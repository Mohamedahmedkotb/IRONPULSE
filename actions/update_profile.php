<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_flash_set('error', 'Invalid request.');
    ip_redirect('pages/profile.php');
}

$full = trim((string) ($_POST['full_name'] ?? ''));
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$gender = mb_substr(trim((string) ($_POST['gender'] ?? '')), 0, 32);
$goal = mb_substr(trim((string) ($_POST['fitness_goal'] ?? '')), 0, 120);
$bio = trim((string) ($_POST['bio'] ?? '')) !== '' ? mb_substr(trim((string) ($_POST['bio'] ?? '')), 0, 2000) : null;
$phone = mb_substr(trim((string) ($_POST['phone'] ?? '')), 0, 40);
$city = mb_substr(trim((string) ($_POST['city'] ?? '')), 0, 120);
$age = null;
if (isset($_POST['age']) && (string) $_POST['age'] !== '') {
    $age = max(0, min(150, (int) $_POST['age']));
}
$height = null;
if (isset($_POST['height']) && (string) $_POST['height'] !== '') {
    $height = (float) $_POST['height'];
}
$weight = null;
if (isset($_POST['weight']) && (string) $_POST['weight'] !== '') {
    $weight = (float) $_POST['weight'];
}
$activity = mb_substr(trim((string) ($_POST['activity_level'] ?? '')), 0, 64);

if (strlen($full) < 2) {
    ip_flash_set('error', 'Full name is required.');
    ip_redirect('pages/profile.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ip_flash_set('error', 'Invalid email.');
    ip_redirect('pages/profile.php');
}

$st = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1');
$st->execute([$email, $userId]);
if ($st->fetch()) {
    ip_flash_set('error', 'That email is already in use.');
    ip_redirect('pages/profile.php');
}

$avatarPath = null;
if (!empty($_FILES['avatar']['tmp_name']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['avatar']['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        ip_flash_set('error', 'Avatar must be JPEG, PNG, or WebP.');
        ip_redirect('pages/profile.php');
    }
    if ($_FILES['avatar']['size'] > 2_097_152) {
        ip_flash_set('error', 'Avatar must be 2MB or smaller.');
        ip_redirect('pages/profile.php');
    }
    $ext = $allowed[$mime];
    $name = 'u' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = dirname(__DIR__) . '/uploads/avatars/' . $name;
    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
        ip_flash_set('error', 'Could not save avatar.');
        ip_redirect('pages/profile.php');
    }
    $avatarPath = 'uploads/avatars/' . $name;
}

$sql = 'UPDATE users SET full_name=?, email=?, gender=?, fitness_goal=?, bio=?, phone=?, city=?, age=?, height=?, weight=?, activity_level=?, updated_at=CURRENT_TIMESTAMP';
$params = [$full, $email, $gender, $goal, $bio, $phone, $city, $age, $height, $weight, $activity];
if ($avatarPath !== null) {
    $sql .= ', avatar=?';
    $params[] = $avatarPath;
}
$sql .= ' WHERE id=?';
$params[] = $userId;

$up = $pdo->prepare($sql);
$up->execute($params);

ip_flash_set('success', 'Profile updated.');
ip_redirect('pages/profile.php');
