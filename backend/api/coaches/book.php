<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'POST') {
    Response::error('Method not allowed', 405);
}

ironpulse_require_csrf_except(['register.php', 'login.php']);

require_once dirname(__DIR__, 2) . '/middleware/require_auth.php';
$uid = require_auth();
$body = Request::jsonBody();
$coachId = (int) ($body['coach_id'] ?? 0);
if ($coachId <= 0 || !CoachRepository::find(Database::pdo(), $coachId)) {
    Response::error('Invalid coach', 422);
}
$bookingDate = Sanitizer::string((string) ($body['booking_date'] ?? date('Y-m-d H:i:s')), 32);
if (strlen($bookingDate) < 10) {
    $bookingDate = date('Y-m-d H:i:s');
}
$pdo = Database::pdo();
$id = BookingRepository::create($pdo, $uid, $coachId, $bookingDate, Sanitizer::string((string) ($body['notes'] ?? ''), 500));
$c = CoachRepository::find($pdo, $coachId);
NotificationService::notify(
    $pdo,
    $uid,
    'booking',
    'Booking request sent',
    'Session with ' . ($c['name'] ?? 'coach') . ' is pending confirmation.',
);
Response::ok('Booking created', ['id' => (string) $id]);
