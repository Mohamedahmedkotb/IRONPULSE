<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

ip_require_login();
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !ip_csrf_validate()) {
    ip_redirect('pages/coaches.php');
}

$coachId = (int) ($_POST['coach_id'] ?? 0);
$when = trim((string) ($_POST['booking_date'] ?? ''));
$notes = mb_substr(trim((string) ($_POST['notes'] ?? '')), 0, 500);

if ($coachId < 1) {
    ip_flash_set('error', 'Select a coach.');
    ip_redirect('pages/coaches.php');
}

$st = $pdo->prepare('SELECT id FROM coaches WHERE id = ? LIMIT 1');
$st->execute([$coachId]);
if (!$st->fetch()) {
    ip_flash_set('error', 'Coach not found.');
    ip_redirect('pages/coaches.php');
}

if ($when === '') {
    $when = date('Y-m-d H:i:s');
} else {
    $when = str_replace('T', ' ', $when);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $when)) {
        $when = date('Y-m-d H:i:s');
    }
}

try {
    $ins = $pdo->prepare(
        'INSERT INTO bookings (user_id, coach_id, booking_date, status, notes) VALUES (?, ?, ?, ?, ?)',
    );
    $ins->execute([$userId, $coachId, $when, 'pending', $notes]);
} catch (PDOException $e) {
    ip_flash_set('error', 'Could not save your booking. Please try again.');
    ip_redirect('pages/coaches.php');
}

ip_flash_set('success', 'Booking request sent.');
ip_redirect('pages/coaches.php');
