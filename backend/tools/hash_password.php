<?php

/** Usage: php backend/tools/hash_password.php "YourPassword" */

declare(strict_types=1);

$pw = $argv[1] ?? '';
if ($pw === '') {
    fwrite(STDERR, "Usage: php hash_password.php \"YourPassword\"\n");
    exit(1);
}
echo password_hash($pw, PASSWORD_DEFAULT) . PHP_EOL;
