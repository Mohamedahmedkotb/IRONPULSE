<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'POST') {
    Response::error('Method not allowed', 405);
}

ironpulse_require_csrf_except(['register.php', 'login.php']);

require_once dirname(__DIR__, 2) . '/middleware/require_auth.php';
$uid = require_auth();
$pdo = Database::pdo();

if (empty($_FILES['avatar']) && empty($_FILES['file'])) {
    Response::error('No file uploaded', 422);
}
$file = $_FILES['avatar'] ?? $_FILES['file'];
$cfg = $ironpulseConfig;
$result = UploadService::storeAvatar($uid, $file, $cfg);
UserRepository::update($pdo, $uid, ['avatar' => $result['public_url']]);

Response::ok('Avatar updated', ['url' => $result['public_url'], 'path' => $result['path']]);
