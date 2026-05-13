<?php

declare(strict_types=1);

/** @param list<string> $dates Y-m-d */
function ironpulse_compute_streak(array $dates): int
{
    $set = [];
    foreach ($dates as $d) {
        $set[substr((string) $d, 0, 10)] = true;
    }
    if ($set === []) {
        return 0;
    }
    $today = new DateTimeImmutable('today');
    $fmt = static fn (DateTimeImmutable $dt) => $dt->format('Y-m-d');
    $check = $today;
    if (!isset($set[$fmt($check)])) {
        $check = $check->modify('-1 day');
        if (!isset($set[$fmt($check)])) {
            return 0;
        }
    }
    $streak = 0;
    while (isset($set[$fmt($check)])) {
        $streak++;
        $check = $check->modify('-1 day');
    }
    return $streak;
}

/** @param array<string,mixed> $row */
function ironpulse_user_public(array $row): array
{
    unset($row['password_hash']);
    return $row;
}

function ironpulse_upload_url(string $relativePath): string
{
    $cfg = require dirname(__DIR__) . '/config/config.php';
    $prefix = $cfg['url_prefix'] ?? '';
    $path = '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
    return $prefix . $path;
}

/** Skip CSRF for listed script basenames (e.g. first auth). */
function ironpulse_require_csrf_except(array $skipBasenames): void
{
    $base = strtolower(basename($_SERVER['SCRIPT_NAME'] ?? ''));
    $skip = array_map('strtolower', $skipBasenames);
    if (in_array($base, $skip, true)) {
        return;
    }
    $m = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    if (in_array($m, ['POST', 'PUT', 'DELETE'], true)) {
        Csrf::requireValid();
    }
}
