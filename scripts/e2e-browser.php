<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$command = $argv[1] ?? 'check';

if ($command !== 'check') {
    fwrite(STDERR, "[E2E Browser] Uso: php scripts/e2e-browser.php check\n");
    exit(1);
}

$failures = [];
if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    $failures[] = 'PHP 8.2 ou superior é obrigatório.';
}
if (! commandExists('node')) {
    $failures[] = 'Node.js não encontrado.';
} elseif (version_compare(trim((string) shell_exec('node --version 2>&1')), 'v18.0.0', '<')) {
    $failures[] = 'Node.js 18 ou superior é obrigatório.';
}
if (! is_file($root.'/.env.e2e')) {
    $failures[] = '.env.e2e ausente; execute composer e2e:prepare.';
}
if (! is_file($root.'/node_modules/@playwright/test/package.json')) {
    $failures[] = '@playwright/test ausente; execute npm ci.';
}
if (! is_file($root.'/playwright.config.ts')) {
    $failures[] = 'playwright.config.ts ausente.';
}
if (! is_dir($root.'/storage/app/e2e/artifacts')) {
    $failures[] = 'Diretório de artefatos ausente; execute composer e2e:prepare.';
}

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "[E2E Browser] {$failure}\n");
    }
    exit(1);
}

fwrite(STDOUT, "[E2E Browser] Runner, ambiente e diretório de artefatos verificados.\n");

function commandExists(string $command): bool
{
    $probe = PHP_OS_FAMILY === 'Windows' ? "where {$command}" : "command -v {$command}";
    exec($probe.' 2>/dev/null', $output, $status);
    return $status === 0;
}
