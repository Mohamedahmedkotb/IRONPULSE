<?php

declare(strict_types=1);

$host = getenv('IRONPULSE_DB_HOST') ?: '127.0.0.1';
$dbname = getenv('IRONPULSE_DB_NAME') ?: 'ironpulse_db';
$username = getenv('IRONPULSE_DB_USER') ?: 'root';
$password = getenv('IRONPULSE_DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . ip_h($e->getMessage()));
}
