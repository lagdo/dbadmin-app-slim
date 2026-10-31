<?php

use App\Facade\RouteParser;
use App\Facade\Session;
use Lagdo\DbAdmin\Support\Provider\AuthInterface;

return [
    // 'default' => null, // No auth.
    'default' => fn() => new class implements AuthInterface {
        public function userId(): string
        {
            return Session::get('user')?->email ?? '';
        }
        public function name(): string
        {
            return Session::get('user')?->name ?? '';
        }
        public function roles(): array
        {
            return [];
        }
        public function audit(): string
        {
            return RouteParser::urlFor('dbaudit_page');
        }
        public function logout(): string
        {
            return RouteParser::urlFor('auth_logout');
        }
    },
];
