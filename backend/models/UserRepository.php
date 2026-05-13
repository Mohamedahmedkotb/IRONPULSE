<?php

declare(strict_types=1);

class UserRepository
{
    public static function findById(PDO $pdo, int $id): ?array
    {
        $st = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $r = $st->fetch();
        return $r ?: null;
    }

    public static function findByEmail(PDO $pdo, string $email): ?array
    {
        $st = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $st->execute([$email]);
        $r = $st->fetch();
        return $r ?: null;
    }

    public static function findByUsername(PDO $pdo, string $username): ?array
    {
        $st = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $st->execute([$username]);
        $r = $st->fetch();
        return $r ?: null;
    }

    /** @param array<string,mixed> $fields */
    public static function create(PDO $pdo, array $fields): int
    {
        $sql = 'INSERT INTO users (username, full_name, email, password_hash, gender, age, height, weight, activity_level, fitness_goal, bio, role, xp, streak)
            VALUES (:username, :full_name, :email, :password_hash, :gender, :age, :height, :weight, :activity_level, :fitness_goal, :bio, :role, 0, 0)';
        $st = $pdo->prepare($sql);
        $st->execute([
            'username' => $fields['username'],
            'full_name' => $fields['full_name'],
            'email' => $fields['email'],
            'password_hash' => $fields['password_hash'],
            'gender' => $fields['gender'] ?? '',
            'age' => $fields['age'] ?? null,
            'height' => $fields['height'] ?? null,
            'weight' => $fields['weight'] ?? null,
            'activity_level' => $fields['activity_level'] ?? '',
            'fitness_goal' => $fields['fitness_goal'] ?? '',
            'bio' => $fields['bio'] ?? '',
            'role' => $fields['role'] ?? 'member',
        ]);
        return (int) $pdo->lastInsertId();
    }

    /** @param array<string,mixed> $patch allowed keys only */
    public static function update(PDO $pdo, int $userId, array $patch): void
    {
        $allowed = [
            'full_name', 'gender', 'age', 'height', 'weight', 'activity_level', 'fitness_goal',
            'bio', 'avatar', 'cover_image', 'phone', 'city', 'email',
        ];
        $sets = [];
        $params = ['id' => $userId];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $patch)) {
                $sets[] = "`$key` = :$key";
                $params[$key] = $patch[$key];
            }
        }
        if ($sets === []) {
            return;
        }
        $sql = 'UPDATE users SET ' . implode(', ', $sets) . ', updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $st = $pdo->prepare($sql);
        $st->execute($params);
    }

    public static function updatePassword(PDO $pdo, int $userId, string $hash): void
    {
        $st = $pdo->prepare('UPDATE users SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        $st->execute([$hash, $userId]);
    }

    public static function touchXpStreak(PDO $pdo, int $userId, int $xpDelta, int $streak): void
    {
        $st = $pdo->prepare('UPDATE users SET xp = xp + ?, streak = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        $st->execute([$xpDelta, $streak, $userId]);
    }
}
