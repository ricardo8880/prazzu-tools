<?php

return [
    'enabled' => (bool) env('E2E_ENABLED', false),

    'base_url' => env('E2E_BASE_URL', 'http://127.0.0.1:8010'),

    'server' => [
        'host' => env('E2E_SERVER_HOST', '127.0.0.1'),
        'port' => (int) env('E2E_SERVER_PORT', 8010),
    ],

    'paths' => [
        'database' => base_path(env('E2E_DATABASE_PATH', 'database/e2e.sqlite')),
        'storage' => base_path(env('E2E_STORAGE_PATH', 'storage/app/e2e')),
        'artifacts' => base_path(env('E2E_ARTIFACTS_PATH', 'storage/app/e2e/artifacts')),
        'downloads' => base_path(env('E2E_DOWNLOADS_PATH', 'storage/app/e2e/downloads')),
        'fixtures' => base_path(env('E2E_FIXTURES_PATH', 'storage/app/e2e/fixtures')),
    ],

    'external_network' => (bool) env('E2E_EXTERNAL_NETWORK', false),

    'profiles' => [
        'free' => [
            'name' => 'E2E Free User',
            'email' => 'e2e.free@prazzu.test',
            'password' => 'E2E-Free-Only-2026!',
            'role' => 'user',
            'subscription_plan' => 'free',
        ],
        'plus' => [
            'name' => 'E2E Plus User',
            'email' => 'e2e.plus@prazzu.test',
            'password' => 'E2E-Plus-Only-2026!',
            'role' => 'user',
            'subscription_plan' => 'plus',
        ],
        'administrator' => [
            'name' => 'E2E Administrator',
            'email' => 'e2e.admin@prazzu.test',
            'password' => 'E2E-Admin-Only-2026!',
            'role' => 'administrator',
            'subscription_plan' => 'plus',
        ],
    ],
];
