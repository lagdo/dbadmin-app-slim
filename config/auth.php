<?php

use Slim\App;

return function(string $email, string $password, App $app) {
    $baseDir = dirname(__DIR__);
    $usersFile = env('USERS_JSON_FILE');
    if (!$usersFile || !is_file("$baseDir/$usersFile")) {
        return null;
    }
    $users = json_decode(file_get_contents("$baseDir/$usersFile"), true) ?? [];
    if (!$users) {
        return null;
    }

    // Authenticate the user.
    $users = array_filter($users, fn(array $user) =>
        ($user['email'] ?? '') === $email &&
            password_verify($password, $user['password'] ?? ''));
    $user = array_shift($users);
    return !$user ? null : (object)$user;
};
