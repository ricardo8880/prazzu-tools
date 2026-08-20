<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$failures = [];

$readJson = static function (string $path) use (&$failures): array {
    if (! is_file($path)) {
        $failures[] = sprintf('Arquivo obrigatório ausente: %s.', $path);

        return [];
    }

    try {
        $decoded = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        $failures[] = sprintf('JSON inválido em %s: %s', $path, $exception->getMessage());

        return [];
    }

    return is_array($decoded) ? $decoded : [];
};

$composer = $readJson($root.'/composer.json');
$package = $readJson($root.'/package.json');
$composerScripts = is_array($composer['scripts'] ?? null) ? $composer['scripts'] : [];
$npmScripts = is_array($package['scripts'] ?? null) ? $package['scripts'] : [];

$commands = [];
$collectCommands = static function (mixed $value) use (&$collectCommands, &$commands): void {
    if (is_array($value)) {
        foreach ($value as $item) {
            $collectCommands($item);
        }

        return;
    }

    if (is_string($value)) {
        $commands[] = $value;
    }
};
$collectCommands($composerScripts);

foreach ($commands as $command) {
    if (preg_match_all('/(?:^|\s)@([A-Za-z0-9:_-]+)/', $command, $matches) === 1 || ! empty($matches[1])) {
        foreach ($matches[1] ?? [] as $scriptName) {
            if ($scriptName === 'php') {
                continue;
            }

            if (! array_key_exists($scriptName, $composerScripts)) {
                $failures[] = sprintf('composer.json referencia alias Composer inexistente: %s.', $scriptName);
            }
        }
    }

    if (preg_match_all('#(?:@php|php|node)\s+(scripts/[A-Za-z0-9_.-]+)#', $command, $matches) === 1 || ! empty($matches[1])) {
        foreach ($matches[1] ?? [] as $relativePath) {
            if (! is_file($root.'/'.$relativePath)) {
                $failures[] = sprintf('composer.json referencia script local inexistente: %s.', $relativePath);
            }
        }
    }

    if (preg_match_all('/npm run ([A-Za-z0-9:_-]+)/', $command, $matches) === 1 || ! empty($matches[1])) {
        foreach ($matches[1] ?? [] as $scriptName) {
            if (! array_key_exists($scriptName, $npmScripts)) {
                $failures[] = sprintf('composer.json referencia script npm inexistente: %s.', $scriptName);
            }
        }
    }
}

foreach ($npmScripts as $scriptName => $command) {
    if (! is_string($command)) {
        continue;
    }

    if (preg_match_all('#(?:node\s+)?(scripts/[A-Za-z0-9_.-]+)#', $command, $matches) === 1 || ! empty($matches[1])) {
        foreach ($matches[1] ?? [] as $relativePath) {
            if (! is_file($root.'/'.$relativePath)) {
                $failures[] = sprintf('package.json [%s] referencia script local inexistente: %s.', $scriptName, $relativePath);
            }
        }
    }
}

$requiredFiles = [
    'README.md',
    'CORE_CANDIDATES.md',
    'docs/IMPLEMENTATION-LOTS.md',
    'docs/PRODUCT-TOOLS-INVENTORY.md',
    'config/product_tools.php',
    'scripts/check-platform.php',
    'scripts/lint-php.php',
    'scripts/e2e-tool-scenarios.php',
    'scripts/verify-distribution.php',
];
foreach ($requiredFiles as $relativePath) {
    if (! is_file($root.'/'.$relativePath)) {
        $failures[] = sprintf('Arquivo constitucional/operacional ausente: %s.', $relativePath);
    }
}

$product = require $root.'/config/product_tools.php';
$official = is_array($product['official'] ?? null) ? $product['official'] : [];
$expected = (int) ($product['expected_module_count'] ?? 0);
if ($expected < 1 || count($official) !== $expected) {
    $failures[] = sprintf('Inventário oficial divergente: %d ferramentas para expected_module_count=%d.', count($official), $expected);
}

$slugs = array_map(static fn (array $tool): string => (string) ($tool['slug'] ?? ''), $official);
if (count($slugs) !== count(array_unique($slugs))) {
    $failures[] = 'Inventário oficial possui slugs duplicados.';
}

$releaseOrders = array_map(static fn (array $tool): int => (int) ($tool['release_order'] ?? 0), $official);
if (in_array(0, $releaseOrders, true) || count($releaseOrders) !== count(array_unique($releaseOrders))) {
    $failures[] = 'Inventário oficial possui release_order ausente/zero ou duplicado.';
}

$failures = array_values(array_unique($failures));
sort($failures);

if ($failures !== []) {
    fwrite(STDERR, "[Repository Integrity] Falhas encontradas:\n - ".implode("\n - ", $failures)."\n");
    exit(1);
}

fwrite(STDOUT, sprintf(
    "[Repository Integrity] OK: %d ferramentas; referências Composer/npm e arquivos operacionais resolvíveis.\n",
    count($official),
));
