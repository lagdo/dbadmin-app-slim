<?php

$baseDir = dirname(__DIR__);

return [
    'app' => [
        'metadata' => [
            'cache' => [
                'enabled' => true,
                'dir' => "$baseDir/storage/dbadmin/attributes",
            ],
        ],
        // 'views' => [
        //     'dbadmin' => [
        //         'directory' => "$baseDir/templates",
        //         'extension' => '.php',
        //         'renderer' => 'jaxon',
        //     ],
        // ],
        'assets' => [
            'export' => true,
            'minify' => true,
            'uri' => '/jaxon/app-0.15.0',
            'dir' => "$baseDir/public/jaxon/app-0.15.0",
            // 'file' => '',
            'include' => [
                'dialog_code' => false,
            ],
        ],
        'dialogs' => [
            // The dialogs libraries are defined in the dbadmin/services/template.php file.
            'lib' => [
                'use' => ['bootstrap5', 'bulma', 'sweetalert', 'cute', 'notyf'],
            ],
        ],
        'storage' => [
            'stores' => [],
        ],
        'upload' => [
            'enabled' => false,
        ],
    ],
    'lib' => [
        'core' => [
            'debug' => [
                'on' => false,
                'verbose' => false,
            ],
            // 'request' => [
            //     'uri' => '',
            // ],
            'prefix' => [
                'class' => '',
            ],
        ],
        'js' => [
            'lib' => [
                'uri' => '/jaxon/lib-5.2.5',
            ],
        ],
    ],
];
