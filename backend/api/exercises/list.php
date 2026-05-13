<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';

if (Request::method() !== 'GET') {
    Response::error('Method not allowed', 405);
}

$q = Sanitizer::string((string) ($_GET['q'] ?? ''), 200);
$muscle = Sanitizer::string((string) ($_GET['muscle'] ?? $_GET['muscle_group'] ?? ''), 80);
$list = ExerciseRepository::list(Database::pdo(), $q !== '' ? $q : null, $muscle !== '' ? $muscle : null);
$mapped = array_map(static function (array $e) {
    $img = $e['image'] ?? '';
    if ($img !== '' && !str_starts_with($img, 'http')) {
        $img = 'https://images.unsplash.com/' . $img . '?auto=format&fit=crop&w=600&q=80';
    }
    return [
        'id' => (int) $e['id'],
        'n' => $e['name'],
        'name' => $e['name'],
        'm' => $e['muscle_group'],
        'muscle_group' => $e['muscle_group'],
        'category' => $e['category'],
        'difficulty' => $e['difficulty'],
        'd' => (string) ($e['instructions'] ?? ''),
        'instructions' => (string) ($e['instructions'] ?? ''),
        'img' => $img,
    ];
}, $list);
Response::ok('OK', ['exercises' => $mapped]);
