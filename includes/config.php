<?php

declare(strict_types=1);

/**
 * Path prefix for URLs (e.g. /IRONPULSE). Auto-detected from SCRIPT_NAME; override with IRONPULSE_BASE env.
 */
function ip_base_path(): string
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $env = getenv('IRONPULSE_BASE');
    if (is_string($env) && $env !== '') {
        $cached = rtrim(str_replace('\\', '/', $env), '/');
        return $cached;
    }
    $sn = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = dirname($sn);
    if (str_ends_with($dir, '/pages')) {
        $parent = dirname($dir);
        $cached = ($parent === '/' || $parent === '.') ? '' : $parent;
        return $cached;
    }
    if (str_ends_with($dir, '/actions')) {
        $parent = dirname($dir);
        $cached = ($parent === '/' || $parent === '.') ? '' : $parent;
        return $cached;
    }
    $cached = ($dir === '/' || $dir === '.') ? '' : $dir;
    return $cached;
}

/** Absolute URL path from site root: /IRONPULSE/assets/... */
function ip_url(string $path): string
{
    $p = '/' . ltrim(str_replace('\\', '/', $path), '/');
    $b = rtrim(ip_base_path(), '/');
    // Never join to "//..." — browsers treat that as protocol-relative (host = first segment).
    if ($b === '' || $b === '/') {
        return $p;
    }
    return $b . $p;
}

function ip_h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function ip_redirect(string $path): never
{
    header('Location: ' . ip_url($path), true, 302);
    exit;
}

function ip_flash_set(string $key, string $message): void
{
    $_SESSION['_flash'][$key] = $message;
}

function ip_flash_get(string $key): ?string
{
    if (!isset($_SESSION['_flash'][$key])) {
        return null;
    }
    $m = (string) $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return $m;
}
