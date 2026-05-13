<?php

declare(strict_types=1);

class MealRepository
{
    public static function listByUser(PDO $pdo, int $userId): array
    {
        $st = $pdo->prepare(
            'SELECT id, user_id, plan_date, title, breakfast, lunch, dinner, calories, protein_g, carbs_g, fats_g
             FROM meal_plans WHERE user_id = ? ORDER BY plan_date DESC',
        );
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    /** @param array<string,mixed> $d */
    public static function upsertDay(PDO $pdo, int $userId, array $d): void
    {
        $sql = 'INSERT INTO meal_plans (user_id, plan_date, title, breakfast, lunch, dinner, calories, protein_g, carbs_g, fats_g)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                title = VALUES(title), breakfast = VALUES(breakfast), lunch = VALUES(lunch), dinner = VALUES(dinner),
                calories = VALUES(calories), protein_g = VALUES(protein_g), carbs_g = VALUES(carbs_g), fats_g = VALUES(fats_g),
                updated_at = CURRENT_TIMESTAMP';
        $st = $pdo->prepare($sql);
        $st->execute([
            $userId,
            $d['plan_date'],
            $d['title'] ?? '',
            $d['breakfast'] ?? '',
            $d['lunch'] ?? '',
            $d['dinner'] ?? '',
            (int) ($d['calories'] ?? 0),
            (int) ($d['protein_g'] ?? 0),
            (int) ($d['carbs_g'] ?? 0),
            (int) ($d['fats_g'] ?? 0),
        ]);
    }
}
