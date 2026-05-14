<?php

declare(strict_types=1);

/** CLI: php tools/hash_password.php "YourPassword" */
$pw = $argv[1] ?? '';
if ($pw === '') {
    fwrite(STDERR, "Usage: php tools/hash_password.php \"YourPassword\"\n");
    exit(1);
}
echo password_hash($pw, PASSWORD_DEFAULT) . "\n";
