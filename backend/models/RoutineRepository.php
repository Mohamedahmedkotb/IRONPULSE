<?php

declare(strict_types=1);

class RoutineRepository
{
    public static function listByUser(PDO $pdo, int $userId): array
    {
        $st = $pdo->prepare('SELECT id, user_id, title, description, created_at, updated_at FROM routines WHERE user_id = ? ORDER BY updated_at DESC');
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public static function find(PDO $pdo, int $id, int $userId): ?array
    {
        $st = $pdo->prepare('SELECT * FROM routines WHERE id = ? AND user_id = ? LIMIT 1');
        $st->execute([$id, $userId]);
        $r = $st->fetch();
        return $r ?: null;
    }

    /** @return list<array{exercise_id:int,sets:int,reps:int,sort_order:int,name?:string}> */
    public static function exercisesForRoutine(PDO $pdo, int $routineId): array
    {
        $st = $pdo->prepare(
            'SELECT re.exercise_id, re.sets, re.reps, re.sort_order, e.name
             FROM routine_exercises re JOIN exercises e ON e.id = re.exercise_id
             WHERE re.routine_id = ? ORDER BY re.sort_order ASC, re.id ASC',
        );
        $st->execute([$routineId]);
        return $st->fetchAll();
    }

    public static function countByUser(PDO $pdo, int $userId): int
    {
        $st = $pdo->prepare('SELECT COUNT(*) FROM routines WHERE user_id = ?');
        $st->execute([$userId]);
        return (int) $st->fetchColumn();
    }

    /**
     * @param list<array{exercise_id?:int|mixed,name?:string,sets:int,reps:int}> $exercises
     */
    public static function createWithExercises(PDO $pdo, int $userId, string $title, string $description, array $exercises): int
    {
        $pdo->beginTransaction();
        try {
            $st = $pdo->prepare('INSERT INTO routines (user_id, title, description) VALUES (?, ?, ?)');
            $st->execute([$userId, $title, $description]);
            $rid = (int) $pdo->lastInsertId();
            $sort = 0;
            $ins = $pdo->prepare(
                'INSERT INTO routine_exercises (routine_id, exercise_id, sort_order, sets, reps) VALUES (?, ?, ?, ?, ?)',
            );
            foreach ($exercises as $ex) {
                $sort++;
                $eid = isset($ex['exercise_id']) ? (int) $ex['exercise_id'] : 0;
                if ($eid <= 0 && !empty($ex['name'])) {
                    $eid = ExerciseRepository::findIdByNameOrCreate($pdo, (string) $ex['name']);
                }
                if ($eid <= 0) {
                    throw new RuntimeException('Each exercise needs exercise_id or name');
                }
                $ins->execute([$rid, $eid, $sort, (int) $ex['sets'], (int) $ex['reps']]);
            }
            $pdo->commit();
            return $rid;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * @param list<array{exercise_id?:int|mixed,name?:string,sets:int,reps:int}> $exercises
     */
    public static function updateWithExercises(PDO $pdo, int $routineId, int $userId, string $title, string $description, array $exercises): void
    {
        $pdo->beginTransaction();
        try {
            $st = $pdo->prepare('UPDATE routines SET title = ?, description = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?');
            $st->execute([$title, $description, $routineId, $userId]);
            if ($st->rowCount() === 0) {
                throw new RuntimeException('Routine not found');
            }
            $pdo->prepare('DELETE FROM routine_exercises WHERE routine_id = ?')->execute([$routineId]);
            $sort = 0;
            $ins = $pdo->prepare(
                'INSERT INTO routine_exercises (routine_id, exercise_id, sort_order, sets, reps) VALUES (?, ?, ?, ?, ?)',
            );
            foreach ($exercises as $ex) {
                $sort++;
                $eid = isset($ex['exercise_id']) ? (int) $ex['exercise_id'] : 0;
                if ($eid <= 0 && !empty($ex['name'])) {
                    $eid = ExerciseRepository::findIdByNameOrCreate($pdo, (string) $ex['name']);
                }
                if ($eid <= 0) {
                    throw new RuntimeException('Each exercise needs exercise_id or name');
                }
                $ins->execute([$routineId, $eid, $sort, (int) $ex['sets'], (int) $ex['reps']]);
            }
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function delete(PDO $pdo, int $id, int $userId): bool
    {
        $st = $pdo->prepare('DELETE FROM routines WHERE id = ? AND user_id = ?');
        $st->execute([$id, $userId]);
        return $st->rowCount() > 0;
    }
}
