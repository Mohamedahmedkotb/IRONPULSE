<?php

declare(strict_types=1);

class ProgressRepository
{
    public static function listByUser(PDO $pdo, int $userId, int $limit = 90): array
    {
        $st = $pdo->prepare(
            'SELECT id, user_id, weight, body_fat, bmi, log_date, notes, created_at
             FROM progress_logs WHERE user_id = ? ORDER BY log_date DESC LIMIT ?',
        );
        $st->bindValue(1, $userId, PDO::PARAM_INT);
        $st->bindValue(2, $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    /** @param array<string,mixed> $d */
    public static function create(PDO $pdo, int $userId, array $d): int
    {
        $st = $pdo->prepare(
            'INSERT INTO progress_logs (user_id, weight, body_fat, bmi, log_date, notes) VALUES (?, ?, ?, ?, ?, ?)',
        );
        $st->execute([
            $userId,
            $d['weight'] ?? null,
            $d['body_fat'] ?? null,
            $d['bmi'] ?? null,
            $d['log_date'],
            $d['notes'] ?? '',
        ]);
        return (int) $pdo->lastInsertId();
    }
}
