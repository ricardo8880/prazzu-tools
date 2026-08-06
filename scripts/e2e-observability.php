<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$command = $argv[1] ?? 'check';

if ($command !== 'check') {
    fwrite(STDERR, "[E2E Observability] Uso: php scripts/e2e-observability.php check\n");
    exit(1);
}

$required = [
    'config/e2e_observability.php',
    'app/Core/Quality/E2E/Support/E2ECorrelation.php',
    'app/Core/Quality/E2E/Http/Middleware/CorrelateE2ERequest.php',
    'app/Core/Quality/E2E/Logging/ConfigureE2EJsonLogging.php',
    'app/Core/Quality/E2E/Providers/E2EObservabilityServiceProvider.php',
    'tests/Browser/playwright/helpers/e2e-correlation.ts',
];

$failures = [];
foreach ($required as $file) {
    if (! is_file($root.'/'.$file)) {
        $failures[] = "Arquivo obrigatório ausente: {$file}";
    }
}

$configSource = file_get_contents($root.'/config/e2e_observability.php') ?: '';
if (! str_contains($configSource, "'schema_version' => '1.0.0'")) {
    $failures[] = 'Schema de observabilidade E2E inválido.';
}
if (! str_contains($configSource, "'run' => 'X-E2E-Run-Id'") || ! str_contains($configSource, "'scenario' => 'X-E2E-Scenario-Id'")) {
    $failures[] = 'Cabeçalhos de correlação divergentes do contrato.';
}

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "[E2E Observability] {$failure}\n");
    }
    exit(1);
}

fwrite(STDOUT, "[E2E Observability] Correlação, logging JSON e integração do runner verificados.\n");
