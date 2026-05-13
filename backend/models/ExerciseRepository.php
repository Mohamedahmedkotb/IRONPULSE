<?php

declare(strict_types=1);

class ExerciseRepository
{
    public static function list(PDO $pdo, ?string $q = null, ?string $muscle = null): array
    {
        $sql = 'SELECT id, name, category, muscle_group, difficulty, instructions, image FROM exercises WHERE 1=1';
        $params = [];
        if ($q !== null && $q !== '') {
            $sql .= ' AND (name LIKE ? OR instructions LIKE ?)';
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if ($muscle !== null && $muscle !== '' && $muscle !== 'All') {
            $sql .= ' AND muscle_group = ?';
            $params[] = $muscle;
        }
        $sql .= ' ORDER BY name ASC';
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public static function findByName(PDO $pdo, string $name): ?array
    {
        $st = $pdo->prepare('SELECT id FROM exercises WHERE name = ? LIMIT 1');
        $st->execute([$name]);
        $r = $st->fetch();
        return $r ?: null;
    }

    public static function findIdByNameOrCreate(PDO $pdo, string $name): int
    {
        $row = self::findByName($pdo, $name);
        if ($row) {
            return (int) $row['id'];
        }
        $st = $pdo->prepare(
            'INSERT INTO exercises (name, category, muscle_group, difficulty, instructions, image) VALUES (?, ?, ?, ?, ?, ?)',
        );
        $st->execute([$name, 'Custom', 'General', 'Intermediate', '', '']);
        return (int) $pdo->lastInsertId();
    }
}
