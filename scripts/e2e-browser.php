<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$command = $argv[1] ?? 'check';

if ($command !== 'check') {
    fwrite(STDERR, "[E2E] Use o comando check.\n");
    exit(1);
}

$playwrightCli = $root.DIRECTORY_SEPARATOR.'node_modules'.DIRECTORY_SEPARATOR.'@playwright'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'cli.js';
if (! is_file($playwrightCli)) {
    fwrite(STDERR, "[E2E] Playwright não instalado. Execute npm ci.\n");
    exit(1);
}

$probe = <<<'JS'
const { chromium } = require('playwright');
const fs = require('node:fs');
const executable = chromium.executablePath();
if (!executable || !fs.existsSync(executable)) process.exit(1);
process.stdout.write(executable);
JS;

$commandLine = escapeshellarg(PHP_OS_FAMILY === 'Windows' ? 'node.exe' : 'node').' -e '.escapeshellarg($probe);
exec($commandLine, $output, $status);

if ($status !== 0) {
    fwrite(STDERR, "[E2E] Chromium não instalado. Execute npm run e2e:install.\n");
    exit(1);
}

fwrite(STDOUT, "[E2E] Playwright e Chromium disponíveis.\n");
