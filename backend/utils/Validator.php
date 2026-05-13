<?php

class Validator
{
    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /** Mirrors frontend passwordStrength: score >= 2 and length >= 8 */
    public static function passwordStrength(string $password): array
    {
        $score = 0;
        $p = $password;
        if (strlen($p) >= 8) {
            $score++;
        }
        if (strlen($p) >= 12) {
            $score++;
        }
        if (preg_match('/[a-z]/', $p) && preg_match('/[A-Z]/', $p)) {
            $score++;
        }
        if (preg_match('/\d/', $p)) {
            $score++;
        }
        if (preg_match('/[^a-zA-Z0-9]/', $p)) {
            $score++;
        }
        return ['score' => $score, 'ok' => strlen($p) >= 8 && $score >= 2];
    }

    public static function username(string $u): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_]{3,32}$/', $u);
    }
}
