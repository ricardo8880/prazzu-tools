<?php

declare(strict_types=1);

return [
    'schema_version' => '1.0.0',
    'enabled' => env('E2E_OBSERVABILITY_ENABLED', false),
    'log_path' => env('E2E_LOG_PATH', storage_path('app/e2e/logs/e2e.jsonl')),
    'slow_query_ms' => (float) env('E2E_SLOW_QUERY_MS', 250),
    'headers' => [
        'run' => 'X-E2E-Run-Id',
        'scenario' => 'X-E2E-Scenario-Id',
    ],
    'sensitive_fields' => [
        'password', 'password_confirmation', 'token', 'authorization', 'cookie',
        'cpf', 'cnpj', 'document', 'email', 'phone', 'address',
    ],
];
