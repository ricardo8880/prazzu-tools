<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$command = $argv[1] ?? 'verify';
$environmentFile = $root.DIRECTORY_SEPARATOR.'.env.e2e';
$exampleFile = $root.DIRECTORY_SEPARATOR.'.env.e2e.example';
$databaseFile = $root.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'e2e.sqlite';
$storageRoot = $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'e2e';

function fail(string $message): never
{
    fwrite(STDERR, "[E2E] {$message}\n");
    exit(1);
}

function ensureDirectory(string $path): void
{
    if (! is_dir($path) && ! mkdir($path, 0775, true) && ! is_dir($path)) {
        fail("Não foi possível criar o diretório [{$path}].");
    }
}

if (! in_array($command, ['prepare', 'verify', 'clean'], true)) {
    fail('Use prepare, verify ou clean.');
}

if ($command === 'clean') {
    foreach ([$databaseFile, $databaseFile.'-shm', $databaseFile.'-wal'] as $file) {
        if (is_file($file) && ! unlink($file)) {
            fail("Não foi possível remover [{$file}].");
        }
    }

    fwrite(STDOUT, "[E2E] Banco isolado removido. Artefatos foram preservados.\n");
    exit(0);
}

if (! is_file($exampleFile)) {
    fail('.env.e2e.example não foi encontrado.');
}

if (! is_file($environmentFile) && ! copy($exampleFile, $environmentFile)) {
    fail('Não foi possível criar .env.e2e a partir do exemplo oficial.');
}

$environment = file_get_contents($environmentFile);
if ($environment === false) {
    fail('Não foi possível ler .env.e2e.');
}

$required = [
    'APP_ENV=e2e',
    'DB_CONNECTION=sqlite',
    'E2E_DATABASE_PATH=database/e2e.sqlite',
    'E2E_EXTERNAL_NETWORK=false',
];

foreach ($required as $entry) {
    if (! str_contains($environment, $entry)) {
        fail("Configuração isolada ausente em .env.e2e: {$entry}");
    }
}

ensureDirectory(dirname($databaseFile));
ensureDirectory($storageRoot);
foreach (['artifacts', 'downloads', 'fixtures', 'logs', 'runtime'] as $directory) {
    ensureDirectory($storageRoot.DIRECTORY_SEPARATOR.$directory);
}

if (! is_file($databaseFile) && ! touch($databaseFile)) {
    fail('Não foi possível criar database/e2e.sqlite.');
}

fwrite(STDOUT, "[E2E] Ambiente isolado verificado.\n");
