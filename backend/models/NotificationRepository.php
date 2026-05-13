<?php

declare(strict_types=1);

class NotificationRepository
{
    public static function list(PDO $pdo, int $userId, int $limit = 50): array
    {
        $st = $pdo->prepare(
            'SELECT id, user_id, type, title, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?',
        );
        $st->bindValue(1, $userId, PDO::PARAM_INT);
        $st->bindValue(2, $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public static function markRead(PDO $pdo, int $userId, ?int $notificationId = null): int
    {
        if ($notificationId !== null) {
            $st = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
            $st->execute([$notificationId, $userId]);
            return $st->rowCount();
        }
        $st = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
        $st->execute([$userId]);
        return $st->rowCount();
    }

    public static function insert(PDO $pdo, int $userId, string $type, string $title, string $message): int
    {
        $st = $pdo->prepare('INSERT INTO notifications (user_id, type, title, message, is_read) VALUES (?, ?, ?, ?, 0)');
        $st->execute([$userId, $type, $title, $message]);
        return (int) $pdo->lastInsertId();
    }
}
