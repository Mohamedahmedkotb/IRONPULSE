<?php

declare(strict_types=1);

class NotificationService
{
    public static function notify(PDO $pdo, int $userId, string $type, string $title, string $message): int
    {
        return NotificationRepository::insert($pdo, $userId, $type, $title, $message);
    }
}
