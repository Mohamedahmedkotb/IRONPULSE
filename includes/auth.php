<?php

declare(strict_types=1);

function ip_require_login(): void
{
    if (!isset($_SESSION['user_id']) || (int) $_SESSION['user_id'] < 1) {
        ip_redirect('pages/login.php');
    }
}

/** Redirect to dashboard if already logged in (login/signup pages). */
function ip_require_guest(): void
{
    if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0) {
        ip_redirect('pages/dashboard.php');
    }
}

/** @return array<string,mixed>|null */
function ip_current_user(PDO $pdo): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $id = (int) $_SESSION['user_id'];
    if ($id < 1) {
        return null;
    }
    $st = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) {
        return null;
    }
    unset($row['password_hash']);
    return $row;
}

function ip_unique_username(PDO $pdo, string $base): string
{
    $u = preg_replace('/[^a-zA-Z0-9_]/', '_', $base);
    $u = substr($u ?: 'user', 0, 28);
    $candidate = $u;
    $n = 0;
    while (true) {
        $st = $pdo->prepare('SELECT 1 FROM users WHERE username = ? LIMIT 1');
        $st->execute([$candidate]);
        if (!$st->fetch()) {
            return $candidate;
        }
        $n++;
        $candidate = $u . '_' . $n;
    }
}

/** @param array<string,mixed> $user */
function ip_user_initials(array $user): string
{
    $name = trim((string) ($user['full_name'] ?? $user['username'] ?? 'IP'));
    $parts = preg_split('/\s+/', $name) ?: [];
    if (count($parts) >= 2) {
        return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
    }
    return strtoupper(mb_substr($name, 0, 2));
}

function ip_password_ok(string $password): array
{
    $score = 0;
    $p = $password;
    if (strlen($p) >= 8) {
        $score++;
    }
    if (strlen($p) >= 12) {
        $score++;
    }
    if (preg_match('/[a-z]/', $p) && preg_match('/[A-Z]/', $p)) {
        $score++;
    }
    if (preg_match('/\d/', $p)) {
        $score++;
    }
    if (preg_match('/[^a-zA-Z0-9]/', $p)) {
        $score++;
    }
    $ok = strlen($p) >= 8 && $score >= 2;
    return ['ok' => $ok, 'score' => $score];
}
