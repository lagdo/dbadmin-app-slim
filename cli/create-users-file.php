<?php

require __DIR__ . '/../vendor/autoload.php';

$usersFile = env('USERS_JSON_FILE', null);
if ($usersFile !== null && !file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode(['users' => []], JSON_PRETTY_PRINT));
}
