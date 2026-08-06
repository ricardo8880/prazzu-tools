<?php

declare(strict_types=1);

return [
    'schema_version' => '1.0.0',
    'catalog' => [
        'expected_tool_count' => 32,
        'required_scenario_kinds' => ['valid', 'invalid'],
    ],
    'complete_projects' => [
        'chromium-desktop',
        'firefox-desktop',
        'webkit-desktop',
        'mobile-chromium',
        'tablet-webkit',
        'access-transversal',
    ],
    'exploration' => [
        'project' => 'exploratory-controlled',
        'seed' => 12012,
        'max_actions_per_tool' => 12,
        'blocking' => false,
    ],
    'health' => [
        'max_flaky_rate' => 0.02,
        'max_skipped_rate' => 0.05,
        'max_suite_duration_ms' => 1_800_000,
    ],
    'retention' => [
        'artifacts_days' => 14,
        'executive_reports_days' => 90,
    ],
    'paths' => [
        'dashboard_json' => 'storage/app/e2e/executive/health.json',
        'dashboard_html' => 'storage/app/e2e/executive/health.html',
        'artifact_root' => 'storage/app/e2e/artifacts',
        'executive_root' => 'storage/app/e2e/executive',
    ],
];
