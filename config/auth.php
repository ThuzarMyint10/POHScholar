<?php

return [

    // Use the admin guard as the application default for packages such as Voyager.
    // Student and admin routes that need an explicit guard continue to select it themselves.
    'defaults' => [
        'guard' => 'admin',
        'passwords' => 'admin_users',
    ],

    'guards' => [
        'admin' => [
            'driver' => 'session',
            'provider' => 'admin_users',
        ],
        'student' => [
            'driver' => 'session',
            'provider' => 'students',
        ],
    ],

    'providers' => [
        'admin_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\AdminUser::class,
        ],
        'students' => [
            'driver' => 'eloquent',
            'model' => App\Models\Student::class,
        ],
    ],

    'passwords' => [
        'admin_users' => [
            'provider' => 'admin_users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'students' => [
            'provider' => 'students',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
