<?php

declare(strict_types=1);

class BookingRepository
{
    public static function create(PDO $pdo, int $userId, int $coachId, string $bookingDate, string $notes = ''): int
    {
        $st = $pdo->prepare(
            'INSERT INTO bookings (user_id, coach_id, booking_date, status, notes) VALUES (?, ?, ?, ?, ?)',
        );
        $st->execute([$userId, $coachId, $bookingDate, 'pending', $notes]);
        return (int) $pdo->lastInsertId();
    }

    public static function listByUser(PDO $pdo, int $userId): array
    {
        $st = $pdo->prepare(
            'SELECT b.*, c.name AS coach_name FROM bookings b JOIN coaches c ON c.id = b.coach_id WHERE b.user_id = ? ORDER BY b.booking_date DESC',
        );
        $st->execute([$userId]);
        return $st->fetchAll();
    }
}
