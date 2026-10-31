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
            'uri' => '/jaxon/app-0.14.0',
            'dir' => "$baseDir/public/jaxon/app-0.14.0",
            // 'file' => '',
            'include' => [
                'dialog_code' => false,
            ],
        ],
        'dialogs' => [
            // Defined in the services/template.php file.
            'lib' => [
                'use' => ['bootstrap5', 'bulma', 'sweetalert', 'cute', 'notyf'],
            ],
        ],
        'storage' => [
            'stores' => [
                'uploads' => [
                    'adapter' => 'local',
                    'dir' => "$baseDir/storage/uploads",
                ],
                'exports' => [
                    'adapter' => 'local',
                    'dir' => "$baseDir/storage/exports",
                ],
            ],
        ],
        'upload' => [
            'enabled' => true,
            'files' => [
                'sql_files' => [
                    'storage' => 'uploads',
                ],
            ],
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
