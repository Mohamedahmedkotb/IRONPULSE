<?php

declare(strict_types=1);

class WorkoutRepository
{
    public static function listByUser(PDO $pdo, int $userId): array
    {
        $st = $pdo->prepare(
            'SELECT id, user_id, title, category, duration, calories, workout_date, notes, created_at
             FROM workouts WHERE user_id = ? ORDER BY workout_date DESC, id DESC',
        );
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public static function find(PDO $pdo, int $id, int $userId): ?array
    {
        $st = $pdo->prepare('SELECT * FROM workouts WHERE id = ? AND user_id = ? LIMIT 1');
        $st->execute([$id, $userId]);
        $r = $st->fetch();
        return $r ?: null;
    }

    /** @param array<string,mixed> $d */
    public static function create(PDO $pdo, int $userId, array $d): int
    {
        $st = $pdo->prepare(
            'INSERT INTO workouts (user_id, title, category, duration, calories, workout_date, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
        );
        $st->execute([
            $userId,
            $d['title'],
            $d['category'],
            $d['duration'],
            $d['calories'],
            $d['workout_date'],
            $d['notes'] ?? '',
        ]);
        return (int) $pdo->lastInsertId();
    }

    /** @param array<string,mixed> $d */
    public static function update(PDO $pdo, int $id, int $userId, array $d): bool
    {
        $st = $pdo->prepare(
            'UPDATE workouts SET title=?, category=?, duration=?, calories=?, workout_date=?, notes=?, updated_at=CURRENT_TIMESTAMP
             WHERE id=? AND user_id=?',
        );
        $st->execute([
            $d['title'],
            $d['category'],
            $d['duration'],
            $d['calories'],
            $d['workout_date'],
            $d['notes'] ?? '',
            $id,
            $userId,
        ]);
        return $st->rowCount() > 0;
    }

    public static function delete(PDO $pdo, int $id, int $userId): bool
    {
        $st = $pdo->prepare('DELETE FROM workouts WHERE id = ? AND user_id = ?');
        $st->execute([$id, $userId]);
        return $st->rowCount() > 0;
    }

    public static function countByUser(PDO $pdo, int $userId): int
    {
        $st = $pdo->prepare('SELECT COUNT(*) FROM workouts WHERE user_id = ?');
        $st->execute([$userId]);
        return (int) $st->fetchColumn();
    }

    public static function sumCaloriesLastDays(PDO $pdo, int $userId, int $days): int
    {
        $st = $pdo->prepare(
            'SELECT COALESCE(SUM(calories),0) FROM workouts WHERE user_id = ? AND workout_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)',
        );
        $st->execute([$userId, $days - 1]);
        return (int) $st->fetchColumn();
    }

    /** @return list<string> */
    public static function distinctWorkoutDates(PDO $pdo, int $userId, int $limit = 400): array
    {
        $st = $pdo->prepare(
            'SELECT DISTINCT workout_date FROM workouts WHERE user_id = ? ORDER BY workout_date DESC LIMIT ?',
        );
        $st->bindValue(1, $userId, PDO::PARAM_INT);
        $st->bindValue(2, $limit, PDO::PARAM_INT);
        $st->execute();
        return array_map('strval', $st->fetchAll(PDO::FETCH_COLUMN) ?: []);
    }

    /** Last N workout dates calories for chart (oldest first) */
    public static function lastSessionsCalories(PDO $pdo, int $userId, int $n): array
    {
        $st = $pdo->prepare(
            'SELECT calories FROM workouts WHERE user_id = ? ORDER BY workout_date DESC, id DESC LIMIT ?',
        );
        $st->bindValue(1, $userId, PDO::PARAM_INT);
        $st->bindValue(2, $n, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_COLUMN);
        return array_reverse(array_map('intval', $rows ?: []));
    }
}
