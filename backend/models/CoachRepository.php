<?php

declare(strict_types=1);

class CoachRepository
{
    public static function list(PDO $pdo): array
    {
        $st = $pdo->query('SELECT id, name, specialty, bio, image, rating, created_at FROM coaches ORDER BY rating DESC, name ASC');
        return $st->fetchAll();
    }

    public static function find(PDO $pdo, int $id): ?array
    {
        $st = $pdo->prepare('SELECT * FROM coaches WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $r = $st->fetch();
        return $r ?: null;
    }
}
