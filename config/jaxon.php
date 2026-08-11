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
        'template' => [
            'name' => 'bootstrap5',
            'assets' => [
                'url' => '/dbadmin',
            ],
        ],
        'views' => [
            'tpl' => [
                'directory' => "$baseDir/templates",
                'extension' => '.php',
                'renderer' => 'jaxon',
            ],
        ],
        'assets' => [
            'export' => true,
            'minify' => true,
            'uri' => '/jaxon/app-0.9.0',
            'dir' => "$baseDir/public/jaxon/app-0.9.0",
            // 'file' => '',
        ],
        'dialogs' => [
            'default' => [
                'modal' => 'bootbox',
                'alert' => 'sweetalert',
                'confirm' => 'sweetalert',
            ],
            'lib' => [
                'use' => ['butterup'],
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
