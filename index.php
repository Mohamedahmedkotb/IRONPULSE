<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0) {
    ip_redirect('pages/dashboard.php');
}

ip_redirect('pages/login.php');
