<?php

declare(strict_types=1);

$root = $argv[1] ?? getcwd();
$root = realpath($root) ?: $root;

$forbiddenPaths = [
    '.env', '.git', '.idea', '.vscode', 'vendor', 'node_modules',
    '.phpunit.cache', '.phpunit.result.cache', 'database/database.sqlite',
    'storage/logs/laravel.log',
];

$violations = [];
foreach ($forbiddenPaths as $relativePath) {
    if (file_exists($root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath))) {
        $violations[] = $relativePath;
    }
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (! $file->isFile()) {
        continue;
    }

    $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
    $name = $file->getFilename();

    if (str_starts_with($name, '~$') || in_array($name, ['.DS_Store', 'Thumbs.db'], true)) {
        $violations[] = $relative;
    }

    if (str_ends_with($name, '.log') && ! str_ends_with($relative, '.gitignore')) {
        $violations[] = $relative;
    }
}

$violations = array_values(array_unique($violations));
sort($violations);

if ($violations !== []) {
    fwrite(STDERR, "Pacote inválido. Arquivos proibidos encontrados:\n - ".implode("\n - ", $violations)."\n");
    exit(1);
}

fwrite(STDOUT, "Pacote de distribuição validado sem resíduos locais.\n");
