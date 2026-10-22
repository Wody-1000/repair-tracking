<?php

use Illuminate\Support\Str;

return [

    // PostgreSQL is the only database used by this project.
    'default' => env('DB_CONNECTION', 'pgsql'),

    'connections' => [

        // Main connection. Use the owner (e.g. postgres) to run migrations,
        // then switch .env to the limited role `repair_app` for day-to-day use.
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'repair_tracking'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'prefer'),
        ],

        // Read-only connection used by the Reports module (database role `repair_readonly`).
        // If DB_READONLY_* are not set it falls back to the main credentials, so the app still works.
        'pgsql_readonly' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'repair_tracking'),
            'username' => env('DB_READONLY_USERNAME', env('DB_USERNAME', 'postgres')),
            'password' => env('DB_READONLY_PASSWORD', env('DB_PASSWORD', '')),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'prefer'),
        ],

    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

];
