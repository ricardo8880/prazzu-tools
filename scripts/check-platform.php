<?php

declare(strict_types=1);

$errors = [];

if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    $errors[] = sprintf('PHP 8.2 ou superior é obrigatório; versão atual: %s.', PHP_VERSION);
}

$requiredExtensions = [
    'dom',
    'mbstring',
    'pdo_sqlite',
    'xml',
    'xmlwriter',
];

foreach ($requiredExtensions as $extension) {
    if (! extension_loaded($extension)) {
        $errors[] = sprintf('Extensão PHP obrigatória ausente: %s.', $extension);
    }
}

$requiredCommands = [
    'node' => 'Node.js 20 ou superior',
    'npm' => 'npm',
];

foreach ($requiredCommands as $command => $label) {
    $version = commandVersion($command);

    if ($version === null) {
        $errors[] = sprintf('%s não foi encontrado no PATH.', $label);

        continue;
    }

    if ($command === 'node' && version_compare(ltrim($version, 'vV'), '20.0.0', '<')) {
        $errors[] = sprintf('Node.js 20 ou superior é obrigatório; versão atual: %s.', $version);
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Ambiente incompatível com o pipeline oficial:\n\n");

    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }

    fwrite(STDERR, "\nConsulte docs/INSTALLATION.md antes de executar composer release:check.\n");

    exit(1);
}

fwrite(STDOUT, sprintf(
    "Ambiente validado: PHP %s, Node.js %s e extensões obrigatórias disponíveis.\n",
    PHP_VERSION,
    commandVersion('node'),
));

function commandVersion(string $command): ?string
{
    $nullDevice = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';
    $process = proc_open(
        sprintf('%s --version 2>%s', escapeshellcmd($command), $nullDevice),
        [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes,
    );

    if (! is_resource($process)) {
        return null;
    }

    fclose($pipes[0]);
    $output = trim((string) stream_get_contents($pipes[1]));
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    return $exitCode === 0 && $output !== '' ? strtok($output, "\r\n") : null;
}
