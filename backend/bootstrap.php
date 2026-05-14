<?php

declare(strict_types=1);

/**
 * Include from endpoints: require_once dirname(__DIR__) . '/bootstrap.php';
 * (api routes are inside the backend directory)
 */
$IRONPULSE_ROOT = __DIR__;

require_once $IRONPULSE_ROOT . '/config/cors.php';
ironpulse_apply_cors();

require_once $IRONPULSE_ROOT . '/utils/Response.php';
require_once $IRONPULSE_ROOT . '/utils/Request.php';
require_once $IRONPULSE_ROOT . '/utils/Sanitizer.php';
require_once $IRONPULSE_ROOT . '/utils/Validator.php';
require_once $IRONPULSE_ROOT . '/utils/Database.php';
require_once $IRONPULSE_ROOT . '/utils/Csrf.php';
require_once $IRONPULSE_ROOT . '/utils/helpers.php';

require_once $IRONPULSE_ROOT . '/models/UserRepository.php';
require_once $IRONPULSE_ROOT . '/models/ExerciseRepository.php';
require_once $IRONPULSE_ROOT . '/models/WorkoutRepository.php';
require_once $IRONPULSE_ROOT . '/models/RoutineRepository.php';
require_once $IRONPULSE_ROOT . '/models/MealRepository.php';
require_once $IRONPULSE_ROOT . '/models/ProgressRepository.php';
require_once $IRONPULSE_ROOT . '/models/CoachRepository.php';
require_once $IRONPULSE_ROOT . '/models/BookingRepository.php';
require_once $IRONPULSE_ROOT . '/models/NotificationRepository.php';

require_once $IRONPULSE_ROOT . '/services/AuthService.php';
require_once $IRONPULSE_ROOT . '/services/NotificationService.php';
require_once $IRONPULSE_ROOT . '/services/UploadService.php';

$ironpulseConfig = require $IRONPULSE_ROOT . '/config/config.php';

ini_set('session.use_strict_mode', '1');
session_name($ironpulseConfig['session_name']);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => ($ironpulseConfig['env'] === 'production'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

set_exception_handler(static function (Throwable $e) use ($ironpulseConfig): void {
    $msg = ($ironpulseConfig['env'] ?? '') === 'production'
        ? 'Server error'
        : $e->getMessage();
    error_log('[IronPulse] ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    Response::error($msg, 500);
});

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});
