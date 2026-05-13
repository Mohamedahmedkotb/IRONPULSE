<?php

return [
    'host' => getenv('IRONPULSE_DB_HOST') ?: '127.0.0.1',
    'port' => (int) (getenv('IRONPULSE_DB_PORT') ?: 3306),
    'database' => getenv('IRONPULSE_DB_NAME') ?: 'ironpulse',
    'username' => getenv('IRONPULSE_DB_USER') ?: 'root',
    'password' => getenv('IRONPULSE_DB_PASS') ?: '',
    'charset' => 'utf8mb4',
];
