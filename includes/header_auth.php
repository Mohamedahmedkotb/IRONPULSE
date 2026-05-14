<?php

declare(strict_types=1);

/** @var string $pageTitle */
/** @var string $authPage login|signup */
$authPage = $authPage ?? 'login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= ip_h($pageTitle ?? 'Account') ?> — Ironpulse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= ip_h(ip_url('assets/css/global.css')) ?>">
    <script>
    window.__IP_URLS__ = <?= json_encode([
        'login' => ip_url('pages/login.php'),
        'signup' => ip_url('pages/signup.php'),
        'dashboard' => ip_url('pages/dashboard.php'),
    ], JSON_THROW_ON_ERROR) ?>;
    </script>
</head>
<body class="auth-page" data-shell="auth" data-page="<?= ip_h($authPage) ?>" data-php-layout="1">
<header id="auth-header-slot" class="auth-header">
<?php require __DIR__ . '/auth_nav.php'; ?>
</header>
