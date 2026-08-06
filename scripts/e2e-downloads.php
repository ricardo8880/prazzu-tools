<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$command = $argv[1] ?? 'check';

if ($command !== 'check') {
    fwrite(STDERR, "[E2E Downloads] Uso: php scripts/e2e-downloads.php check\n");
    exit(1);
}

require $root.'/vendor/autoload.php';
$config = require $root.'/config/e2e_scenarios.php';
$failures = [];
$count = 0;
$formats = [];

foreach (($config['tools'] ?? []) as $slug => $scenarios) {
    foreach ($scenarios as $scenario) {
        if (! $scenario instanceof App\Core\Quality\E2E\Data\ToolScenario) {
            continue;
        }
        foreach ($scenario->downloads as $download) {
            if (! $download instanceof App\Core\Quality\E2E\Data\ToolDownloadExpectation) {
                $failures[] = "Download inválido em [{$slug}:{$scenario->id}].";
                continue;
            }
            $count++;
            $formats[$download->format] = true;
            if ($download->format === 'xlsx' && ! in_array('xl/workbook.xml', $download->requiredEntries, true)) {
                $failures[] = "XLSX [{$slug}:{$scenario->id}:{$download->id}] deve exigir xl/workbook.xml.";
            }
            if ($download->format === 'docx' && ! in_array('word/document.xml', $download->requiredEntries, true)) {
                $failures[] = "DOCX [{$slug}:{$scenario->id}:{$download->id}] deve exigir word/document.xml.";
            }
        }
    }
}

$requiredFiles = [
    'app/Core/Quality/E2E/Data/ToolDownloadExpectation.php',
    'tests/Browser/playwright/helpers/download-validator.ts',
    'tests/Browser/playwright/tool-downloads.spec.ts',
];
foreach ($requiredFiles as $file) {
    if (! is_file($root.'/'.$file)) {
        $failures[] = "Arquivo obrigatório ausente: {$file}.";
    }
}

if ($count < 2) {
    $failures[] = 'O lote piloto deve declarar ao menos dois downloads reais.';
}

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "[E2E Downloads] {$failure}\n");
    }
    exit(1);
}

ksort($formats);
fwrite(STDOUT, sprintf(
    "[E2E Downloads] %d downloads declarados e validação profunda ativa para: %s.\n",
    $count,
    implode(', ', array_keys($formats)),
));
