<?php

class Response
{
    public static function json(bool $success, string $message, mixed $data = null, int $httpCode = 200): never
    {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], JSON_THROW_ON_ERROR);
        exit;
    }

    public static function error(string $message, int $httpCode = 400, mixed $data = null): never
    {
        self::json(false, $message, $data, $httpCode);
    }

    public static function ok(string $message, mixed $data = null): never
    {
        self::json(true, $message, $data, 200);
    }
}
