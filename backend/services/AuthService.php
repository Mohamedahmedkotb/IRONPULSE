<?php

declare(strict_types=1);

class AuthService
{
    public static function uniqueUsername(PDO $pdo, string $base): string
    {
        $u = preg_replace('/[^a-zA-Z0-9_]/', '_', $base);
        $u = substr($u ?: 'user', 0, 28);
        $candidate = $u;
        $n = 0;
        while (UserRepository::findByUsername($pdo, $candidate)) {
            $n++;
            $candidate = $u . '_' . $n;
        }
        return $candidate;
    }

    /** @return array{user: array, csrf_token: string} */
    public static function login(PDO $pdo, string $email, string $password): array
    {
        $row = UserRepository::findByEmail($pdo, $email);
        if (!$row || !password_verify($password, $row['password_hash'])) {
            Response::error('Invalid credentials', 401);
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $row['id'];
        $_SESSION['user_email'] = $row['email'];
        $token = Csrf::token();
        return ['user' => ironpulse_user_public($row), 'csrf_token' => $token];
    }

    /**
     * @param array{full_name:string,email:string,password:string,fitness_goal?:string,gender?:string} $input
     * @return array{user: array, csrf_token: string}
     */
    public static function register(PDO $pdo, array $input): array
    {
        $email = Sanitizer::email($input['email']);
        if (!Validator::email($email)) {
            Response::error('Invalid email address', 422);
        }
        $pw = (string) $input['password'];
        $strength = Validator::passwordStrength($pw);
        if (!$strength['ok']) {
            Response::error('Password must be at least 8 characters with mixed character types (see strength meter).', 422);
        }
        if (UserRepository::findByEmail($pdo, $email)) {
            Response::error('Email already registered', 409);
        }
        $full = Sanitizer::string($input['full_name'], 120);
        if (strlen($full) < 2) {
            Response::error('Please enter your full name.', 422);
        }
        $username = self::uniqueUsername($pdo, strstr($email, '@', true) ?: 'user');
        $goal = Sanitizer::string($input['fitness_goal'] ?? '', 120);
        $gender = Sanitizer::string($input['gender'] ?? '', 32);
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $id = UserRepository::create($pdo, [
            'username' => $username,
            'full_name' => $full,
            'email' => $email,
            'password_hash' => $hash,
            'gender' => $gender,
            'fitness_goal' => $goal,
        ]);
        $row = UserRepository::findById($pdo, $id);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        $_SESSION['user_email'] = $email;
        $token = Csrf::token();
        NotificationRepository::insert($pdo, $id, 'welcome', 'Welcome to Ironpulse', 'Your account is ready. Log your first workout!');
        return ['user' => ironpulse_user_public($row), 'csrf_token' => $token];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}
