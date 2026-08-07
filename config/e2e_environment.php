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

];
