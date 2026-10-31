<?php

use App\Facade\RouteParser;
use Lagdo\DbAdmin\Support\Facade\Auth;
use Lagdo\DbAdmin\Support\Service\Export\AbstractFileSystem;
use Pharaonic\Slugify\Facades\Slugify;

return [
    // 'default' => null, // No export.
    'default' => fn() => new class extends AbstractFileSystem {
        protected function storage(): string
        {
            return 'exports';
        }
        protected function path(string $filename): string
        {
            // Use the slugified username to customize the path.
            $userId = Auth::userId();
            $userDir = $userId === '' ? 'users' : 'users/' . Slugify::get($userId);
            return "$userDir/$filename";
        }
        protected function url(string $filename): string
        {
            return RouteParser::urlFor('dbadmin_file', ['filename' => $filename]);
        }
    },
];
