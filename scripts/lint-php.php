<?php

declare(strict_types=1);

$paths = [
    'app',
    'bootstrap',
    'config',
    'database',
    'routes',
    'scripts',
    'tests',
    'artisan',
];

$files = [];

foreach ($paths as $path) {
    if (is_file($path)) {
        $files[] = $path;

        continue;
    }

    if (! is_dir($path)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
}

sort($files);

foreach ($files as $file) {
    $command = sprintf('%s -l %s', escapeshellarg(PHP_BINARY), escapeshellarg($file));
    passthru($command, $exitCode);

    if ($exitCode !== 0) {
        exit($exitCode);
    }
}

fwrite(STDOUT, sprintf("Sintaxe validada em %d arquivos PHP do projeto.\n", count($files)));
