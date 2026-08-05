<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$command = $argv[1] ?? 'export';

if (! in_array($command, ['export', 'check'], true)) {
    fwrite(STDERR, "[E2E Catalog] Uso: php scripts/e2e-tool-catalog.php [export|check]\n");
    exit(1);
}

$product = require $root.'/config/product_tools.php';
$quality = require $root.'/config/e2e_quality.php';
$official = array_values($product['official'] ?? []);
$inventory = array_values($quality['tools'] ?? []);
$expected = (int) ($quality['expected_tool_count'] ?? 0);
$failures = [];

if (count($official) !== $expected) {
    $failures[] = sprintf('Catálogo oficial possui %d ferramentas; esperado: %d.', count($official), $expected);
}
if (count($inventory) !== $expected) {
    $failures[] = sprintf('Inventário E2E possui %d ferramentas; esperado: %d.', count($inventory), $expected);
}

$inventoryBySlug = [];
foreach ($inventory as $tool) {
    $slug = trim((string) ($tool['slug'] ?? ''));
    if ($slug === '' || isset($inventoryBySlug[$slug])) {
        $failures[] = "Slug E2E vazio ou duplicado: [{$slug}].";
        continue;
    }
    $inventoryBySlug[$slug] = $tool;
}

$manifest = [];
foreach ($official as $tool) {
    $slug = trim((string) ($tool['slug'] ?? ''));
    $module = trim((string) ($tool['module'] ?? ''));
    $qualityTool = $inventoryBySlug[$slug] ?? null;

    if ($slug === '' || $module === '') {
        $failures[] = 'Ferramenta oficial sem slug ou módulo.';
        continue;
    }
    if (! is_array($qualityTool)) {
        $failures[] = "Ferramenta oficial [{$slug}] ausente do inventário E2E.";
        continue;
    }
    if (($qualityTool['module'] ?? null) !== $module) {
        $failures[] = "Módulo divergente para [{$slug}].";
    }
    if (! in_array('page_load', $qualityTool['required_scenarios'] ?? [], true)) {
        $failures[] = "Ferramenta [{$slug}] não declara o cenário page_load.";
    }
    if (! in_array('form', $qualityTool['surfaces'] ?? [], true)) {
        $failures[] = "Ferramenta [{$slug}] não declara a superfície form.";
    }

    $manifest[] = [
        'id' => (int) ($tool['id'] ?? 0),
        'key' => (string) ($tool['key'] ?? ''),
        'name' => (string) ($tool['name'] ?? ''),
        'module' => $module,
        'slug' => $slug,
        'path' => '/ferramentas/'.$slug,
        'risk' => (string) ($qualityTool['risk'] ?? ''),
        'surfaces' => array_values($qualityTool['surfaces'] ?? []),
        'download_formats' => array_values($qualityTool['download_formats'] ?? []),
        'test_ids' => [
            'page' => 'tool-page-'.$slug,
            'form' => 'tool-form-panel',
        ],
    ];
}

usort($manifest, static fn (array $left, array $right): int => $left['id'] <=> $right['id']);

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "[E2E Catalog] {$failure}\n");
    }
    exit(1);
}

$payload = [
    'schema_version' => '1.0.0',
    'generated_from' => ['config/product_tools.php', 'config/e2e_quality.php'],
    'tool_count' => count($manifest),
    'tools' => $manifest,
];

if ($command === 'check') {
    fwrite(STDOUT, sprintf("[E2E Catalog] %d ferramentas oficiais e inventário E2E estão sincronizados.\n", count($manifest)));
    exit(0);
}

$output = $root.'/'.ltrim((string) ($quality['runtime']['catalog_manifest'] ?? 'storage/app/e2e/runtime/tool-catalog.json'), '/');
$directory = dirname($output);
if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
    fwrite(STDERR, "[E2E Catalog] Não foi possível criar [{$directory}].\n");
    exit(1);
}

$json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL;
if (file_put_contents($output, $json, LOCK_EX) === false) {
    fwrite(STDERR, "[E2E Catalog] Não foi possível gravar [{$output}].\n");
    exit(1);
}

fwrite(STDOUT, sprintf("[E2E Catalog] Manifesto com %d ferramentas gerado em [%s].\n", count($manifest), $output));
