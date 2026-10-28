<?php

return [

    'default' => env('FILESYSTEM_DISK', 'public'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],

        // Publicly-served uploads: student photos, certificate/document
        // PDFs, academic result files. `php artisan storage:link` creates
        // the public/storage symlink (on Windows, run that command from an
        // elevated prompt, or copy the folder if symlinks are blocked -
        // see the deployment guide).
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
