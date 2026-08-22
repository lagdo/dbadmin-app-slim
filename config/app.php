<?php

use Monolog\Level;

return [
    // See https://www.slimframework.com/docs/v4/middleware/error-handling.html#usage
    'errors' => [
        'displayDetails' => false, // Should be set to false in production
        'log' => true,
        'logDetails' => true,
    ],
    // See https://github.com/slimphp/Slim-Skeleton/blob/main/app/settings.php
    'logger' => [
        'name' => 'dbadmin',
        'path' => __DIR__ . '/../storage/logs/app.log',
        'level' => Level::Debug,
    ],
    // See https://github.com/bryanjhv/slim-session
    'session' => [
        'name' => 'dbadmin_session',
        'autorefresh' => true,
        'lifetime' => '1 hour',
    ],
];
