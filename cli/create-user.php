<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Command\CreateUser;
use Dotenv\Dotenv;

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
(new CreateUser())->run();
