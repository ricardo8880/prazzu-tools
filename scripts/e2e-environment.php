<?php

declare(strict_types=1);

const E2E_ENVIRONMENT = 'e2e';

$root = dirname(__DIR__);
$command = $argv[1] ?? 'help';
$envExample = $root.'/.env.e2e.example';
$envFile = $root.'/.env.e2e';

try {
    match ($command) {
        'prepare' => prepareEnvironment($root, $envExample, $envFile),
        'verify' => verifyEnvironment($root, $envFile),
        'clean' => cleanEnvironment($root, $envFile),
        default => showHelp(),
    };
} catch (Throwable $exception) {
    fwrite(STDERR, '[E2E] '.$exception->getMessage().PHP_EOL);
    exit(1);
}

function prepareEnvironment(string $root, string $envExample, string $envFile): void
{
    if (! is_file($envExample)) {
        throw new RuntimeException('Arquivo .env.e2e.example não encontrado.');
    }

    if (! is_file($envFile)) {
        if (! copy($envExample, $envFile)) {
            throw new RuntimeException('Não foi possível criar .env.e2e.');
        }

        output('Arquivo .env.e2e criado a partir do exemplo seguro.');
    }

    $environment = readEnvironment($envFile);
    assertSafeEnvironment($root, $environment);

    foreach (e2eDirectories($root, $environment) as $directory) {
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException("Não foi possível criar o diretório isolado: {$directory}");
        }
    }

    $database = absolutePath($root, requiredValue($environment, 'DB_DATABASE'));
    if (! is_file($database) && file_put_contents($database, '') === false) {
        throw new RuntimeException('Não foi possível criar o banco SQLite E2E.');
    }

    runArtisan($root, ['migrate:fresh', '--seed', '--seeder=Database\\Seeders\\E2EEnvironmentSeeder', '--force', '--env=e2e']);
    verifyEnvironment($root, $envFile);
    output('Ambiente E2E preparado com banco, perfis e storage isolados.');
}

function verifyEnvironment(string $root, string $envFile): void
{
    if (! is_file($envFile)) {
        throw new RuntimeException('Execute composer e2e:prepare para criar .env.e2e.');
    }

    $environment = readEnvironment($envFile);
    assertSafeEnvironment($root, $environment);

    $database = absolutePath($root, requiredValue($environment, 'DB_DATABASE'));
    if (! is_file($database)) {
        throw new RuntimeException('Banco SQLite E2E ainda não foi criado.');
    }

    foreach (e2eDirectories($root, $environment) as $directory) {
        if (! is_dir($directory)) {
            throw new RuntimeException("Diretório E2E ausente: {$directory}");
        }
    }

    output('Isolamento E2E verificado com sucesso.');
}

function cleanEnvironment(string $root, string $envFile): void
{
    if (! is_file($envFile)) {
        output('Nenhum .env.e2e encontrado; não há ambiente para limpar.');
        return;
    }

    $environment = readEnvironment($envFile);
    assertSafeEnvironment($root, $environment);

    $database = absolutePath($root, requiredValue($environment, 'DB_DATABASE'));
    removeFile($database);
    removeFile($database.'-shm');
    removeFile($database.'-wal');

    foreach (array_reverse(e2eDirectories($root, $environment)) as $directory) {
        removeDirectory($directory);
    }

    output('Banco e artefatos E2E removidos. O arquivo .env.e2e foi preservado.');
}

function assertSafeEnvironment(string $root, array $environment): void
{
    $expected = [
        'APP_ENV' => 'e2e',
        'DB_CONNECTION' => 'sqlite',
        'CACHE_STORE' => 'array',
        'SESSION_DRIVER' => 'file',
        'QUEUE_CONNECTION' => 'sync',
        'QUEUE_FAILED_DRIVER' => 'null',
        'MAIL_MAILER' => 'array',
        'FILESYSTEM_DISK' => 'e2e',
        'E2E_ENABLED' => 'true',
        'E2E_EXTERNAL_NETWORK' => 'false',
        'E2E_OBSERVABILITY_ENABLED' => 'true',
    ];

    foreach ($expected as $key => $value) {
        if (strtolower(requiredValue($environment, $key)) !== strtolower($value)) {
            throw new RuntimeException("Configuração insegura em {$key}; esperado {$value}.");
        }
    }

    $database = absolutePath($root, requiredValue($environment, 'DB_DATABASE'));
    $allowedDatabase = realpath($root.'/database') ?: $root.'/database';
    if (! pathIsInside($database, $allowedDatabase) || basename($database) !== 'e2e.sqlite') {
        throw new RuntimeException('DB_DATABASE deve apontar exclusivamente para database/e2e.sqlite.');
    }

    $storage = absolutePath($root, requiredValue($environment, 'E2E_STORAGE_PATH'));
    $allowedStorage = realpath($root.'/storage/app') ?: $root.'/storage/app';
    if (! pathIsInside($storage, $allowedStorage) || basename($storage) !== 'e2e') {
        throw new RuntimeException('E2E_STORAGE_PATH deve apontar exclusivamente para storage/app/e2e.');
    }
}

function readEnvironment(string $path): array
{
    $values = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $values[trim($key)] = trim(trim($value), "\"'");
    }

    return $values;
}

function requiredValue(array $environment, string $key): string
{
    $value = trim((string) ($environment[$key] ?? ''));
    if ($value === '') {
        throw new RuntimeException("Variável obrigatória ausente: {$key}");
    }

    return $value;
}

function e2eDirectories(string $root, array $environment): array
{
    return array_values(array_unique([
        absolutePath($root, requiredValue($environment, 'E2E_STORAGE_PATH')),
        absolutePath($root, requiredValue($environment, 'E2E_ARTIFACTS_PATH')),
        absolutePath($root, requiredValue($environment, 'E2E_DOWNLOADS_PATH')),
        absolutePath($root, requiredValue($environment, 'E2E_FIXTURES_PATH')),
        absolutePath($root, requiredValue($environment, 'SESSION_FILES_PATH')),
        dirname(absolutePath($root, requiredValue($environment, 'E2E_LOG_PATH'))),
    ]));
}

function absolutePath(string $root, string $path): string
{
    if (preg_match('/^(?:[A-Za-z]:[\\\\\/]|\/)/', $path) === 1) {
        return normalizePath($path);
    }

    return normalizePath($root.'/'.$path);
}

function normalizePath(string $path): string
{
    $parts = [];
    foreach (preg_split('~[\\\\/]+~', $path) ?: [] as $part) {
        if ($part === '' || $part === '.') {
            continue;
        }
        if ($part === '..') {
            array_pop($parts);
            continue;
        }
        $parts[] = $part;
    }

    $prefix = str_starts_with(str_replace('\\', '/', $path), '/') ? '/' : '';
    if (preg_match('/^[A-Za-z]:/', $path, $match) === 1) {
        $prefix = $match[0].'/';
        if (($parts[0] ?? null) === $match[0]) {
            array_shift($parts);
        }
    }

    return $prefix.implode('/', $parts);
}

function pathIsInside(string $path, string $directory): bool
{
    $path = rtrim(normalizePath($path), '/').'/';
    $directory = rtrim(normalizePath($directory), '/').'/';

    return str_starts_with(strtolower($path), strtolower($directory));
}

function runArtisan(string $root, array $arguments): void
{
    $command = array_merge([PHP_BINARY, $root.'/artisan'], $arguments);
    $escaped = implode(' ', array_map('escapeshellarg', $command));
    passthru($escaped, $status);

    if ($status !== 0) {
        throw new RuntimeException('O comando Artisan do ambiente E2E falhou.');
    }
}

function removeFile(string $path): void
{
    if (is_file($path) && ! unlink($path)) {
        throw new RuntimeException("Não foi possível remover: {$path}");
    }
}

function removeDirectory(string $path): void
{
    if (! is_dir($path)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST,
    );

    foreach ($iterator as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }

    rmdir($path);
}

function output(string $message): void
{
    fwrite(STDOUT, '[E2E] '.$message.PHP_EOL);
}

function showHelp(): void
{
    output('Uso: php scripts/e2e-environment.php prepare|verify|clean');
}
