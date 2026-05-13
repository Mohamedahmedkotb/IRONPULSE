<?php

/**
 * Application config (override via environment variables when available).
 */
return [
    'app_name' => getenv('IRONPULSE_APP_NAME') ?: 'Ironpulse',
    'env' => getenv('IRONPULSE_ENV') ?: 'development',
    'upload_max_bytes' => (int) (getenv('IRONPULSE_UPLOAD_MAX') ?: 2_097_152),
    'upload_allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
    'upload_allowed_ext' => ['jpg', 'jpeg', 'png', 'webp'],
    /** Web path prefix when app lives in subdirectory (e.g. /IRONPULSE). Empty if docroot is project root. */
    'url_prefix' => rtrim((string) (getenv('IRONPULSE_URL_PREFIX') ?: ''), '/'),
    'session_name' => 'IRONPULSESESSID',
    'csrf_header' => 'X-CSRF-Token',
];
