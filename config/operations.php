<?php

return [
    'security_headers' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
    ],
    'content_security_policy' => [
        'enabled' => (bool) env(
            'SECURITY_CSP_ENABLED',
            env('APP_ENV', 'production') === 'production'
        ),
        'value' => env(
            'SECURITY_CONTENT_POLICY',
            "default-src 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline'; script-src 'self'; font-src 'self' data:; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'"
        ),
    ],
    'hsts' => env('SECURITY_HSTS', 'max-age=31536000; includeSubDomains'),
    'sensitive_log_keys' => [
        'password', 'password_confirmation', 'token', 'access_token', 'refresh_token',
        'authorization', 'cookie', 'cpf', 'cnpj', 'inscricao_estadual', 'salary',
        'salario', 'remuneracao', 'secret', 'api_key',
    ],
    'retention' => [
        'application_logs_days' => (int) env('RETENTION_APPLICATION_LOGS_DAYS', 14),
        'temporary_files_hours' => (int) env('RETENTION_TEMPORARY_FILES_HOURS', 24),
        'failed_jobs_days' => (int) env('RETENTION_FAILED_JOBS_DAYS', 30),
    ],
    'recovery' => [
        'backup_frequency' => env('BACKUP_FREQUENCY', 'daily'),
        'backup_retention_days' => (int) env('BACKUP_RETENTION_DAYS', 30),
        'rpo_hours' => (int) env('RECOVERY_POINT_OBJECTIVE_HOURS', 24),
        'rto_hours' => (int) env('RECOVERY_TIME_OBJECTIVE_HOURS', 4),
    ],
];
