<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'carg' => [
            'driver' => 'sftp',
            'host' => env('TGEN_HOST'),
            'username' => 'root',
            'password' => env('TGEN_PASSWORD'),
            'root' => env('CARG_ROOT'),
            'port' => 22,
            'timeout' => 30,
            'visibility' => 'public',
            'directory_visibility' => 'public',
        ],

        'blankmap' => [
            'driver' => 'sftp',
            'host' => env('TGEN_HOST'),
            'username' => 'root',
            'password' => env('TGEN_PASSWORD'),
            'root' => env('BLANKMAP_ROOT'),
            'port' => 22,
            'timeout' => 30,
            'visibility' => 'public',
            'directory_visibility' => 'public',
        ],

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        'tiles' => [
            'driver' => 'local',
            'root'   => public_path('tiles'),
            'url'    => env('APP_URL') . '/tiles',
            'visibility' => 'public',
        ],
        'cargziptiles_legacy' => [
            'driver' => 's3',
            'key' => env('AWS_CARGZIPTILES_ACCESS_KEY_ID'),
            'secret' => env('AWS_CARGZIPTILES_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_CARGZIPTILES_BUCKET', 'cargziptiles'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        'cargziptiles' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => 'wmfe',
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'root' => 'carg/cargziptiles',
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'public',
            'throw' => false,
        ],

        's3_ispra' => [
            'driver' => 's3',
            'key' => env('AWS_ISPRA_ACCESS_KEY_ID'),
            'secret' => env('AWS_ISPRA_SECRET_ACCESS_KEY'),
            'region' => env('AWS_ISPRA_DEFAULT_REGION', 'eu-central-1'),
            'bucket' => env('AWS_ISPRA_BUCKET'),
        ],
    ],



    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
