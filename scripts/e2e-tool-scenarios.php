<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$command = $argv[1] ?? 'export';

if (! in_array($command, ['export', 'check'], true)) {
    fwrite(STDERR, "[E2E Scenarios] Uso: php scripts/e2e-tool-scenarios.php [export|check]\n");
    exit(1);
}

require $root.'/vendor/autoload.php';
$config = require $root.'/config/e2e_scenarios.php';
$product = require $root.'/config/product_tools.php';
$verticals = require $root.'/config/verticals.php';
$officialTools = $product['official'] ?? [];
$allowedSlugs = array_column($officialTools, 'slug');
$toolVerticals = [];
foreach ($officialTools as $tool) {
    $slug = (string) ($tool['slug'] ?? '');
    $vertical = (string) ($tool['vertical'] ?? '');
    if ($slug !== '' && $vertical !== '') {
        $toolVerticals[$slug] = $vertical;
    }
}
$allowedActions = $config['allowed_step_actions'] ?? [];
$allowedExpectations = $config['allowed_expectations'] ?? [];
$failures = [];
$scenarios = [];
$seen = [];

foreach (($config['tools'] ?? []) as $slug => $toolScenarios) {
    if (! in_array($slug, $allowedSlugs, true)) {
        $failures[] = "Ferramenta desconhecida no catálogo oficial: [{$slug}].";
    }
    foreach ($toolScenarios as $scenario) {
        if (! $scenario instanceof App\Core\Quality\E2E\Data\ToolScenario) {
            $failures[] = "Entrada de cenário inválida para [{$slug}].";
            continue;
        }
        $data = $scenario->toArray();
        $vertical = $toolVerticals[$slug] ?? null;
        $publicVertical = is_string($vertical) ? ($verticals['registered'][$vertical]['public_slug'] ?? null) : null;
        if (! is_string($publicVertical) || trim($publicVertical) === '') {
            $failures[] = "Ferramenta [{$slug}] não possui vertical pública E2E resolvível.";
        } else {
            $data['vertical_public_slug'] = $publicVertical;
        }
        $key = $slug.':'.$data['id'];
        if (isset($seen[$key])) {
            $failures[] = "Cenário duplicado: [{$key}].";
        }
        $seen[$key] = true;
        if ($data['tool_slug'] !== $slug) {
            $failures[] = "Slug divergente no cenário [{$key}].";
        }
        foreach ($data['steps'] as $index => $step) {
            if (! in_array($step['action'] ?? null, $allowedActions, true)) {
                $failures[] = "Ação inválida no cenário [{$key}] etapa {$index}.";
            }
            if (! in_array(($step['action'] ?? null), ['submit', 'auto_fill_form', 'invalidate_required'], true) && trim((string) ($step['test_id'] ?? '')) === '') {
                $failures[] = "Etapa {$index} de [{$key}] não informa test_id.";
            }
        }
        foreach ($data['expectations'] as $index => $expectation) {
            if (! in_array($expectation['type'] ?? null, $allowedExpectations, true)) {
                $failures[] = "Expectativa inválida no cenário [{$key}] índice {$index}.";
            }
        }
        $downloadIds = [];
        foreach ($data['downloads'] as $index => $download) {
            $downloadId = trim((string) ($download['id'] ?? ''));
            if ($downloadId === '' || isset($downloadIds[$downloadId])) {
                $failures[] = "Download inválido ou duplicado no cenário [{$key}] índice {$index}.";
            }
            $downloadIds[$downloadId] = true;
            if (trim((string) ($download['test_id'] ?? '')) === '') {
                $failures[] = "Download [{$key}:{$downloadId}] não informa test_id.";
            }
            if (! in_array($download['format'] ?? null, ['pdf', 'xlsx', 'csv', 'docx', 'zip'], true)) {
                $failures[] = "Formato inválido no download [{$key}:{$downloadId}].";
            }
            if ((int) ($download['minimum_bytes'] ?? 0) < 1) {
                $failures[] = "Tamanho mínimo inválido no download [{$key}:{$downloadId}].";
            }
            foreach (($download['required_entries'] ?? []) as $entry) {
                if (trim((string) $entry) === '') {
                    $failures[] = "Entrada ZIP vazia no download [{$key}:{$downloadId}].";
                }
            }
        }
        $scenarios[] = $data;
    }
}

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "[E2E Scenarios] {$failure}\n");
    }
    exit(1);
}

usort($scenarios, static fn (array $a, array $b): int => [$a['tool_slug'], $a['id']] <=> [$b['tool_slug'], $b['id']]);
$payload = [
    'schema_version' => (string) ($config['schema_version'] ?? ''),
    'generated_from' => ['config/e2e_scenarios.php', 'config/product_tools.php'],
    'scenario_count' => count($scenarios),
    'tool_count' => count(array_unique(array_column($scenarios, 'tool_slug'))),
    'coverage' => [
        'valid_tools' => count(array_unique(array_column(array_filter($scenarios, static fn (array $scenario): bool => $scenario['kind'] === 'valid'), 'tool_slug'))),
        'invalid_tools' => count(array_unique(array_column(array_filter($scenarios, static fn (array $scenario): bool => $scenario['kind'] === 'invalid'), 'tool_slug'))),
    ],
    'scenarios' => $scenarios,
];

if ($payload['tool_count'] !== (int) ($config['minimum_coverage']['expected_tool_count'] ?? 0)) {
    fwrite(STDERR, sprintf("[E2E Scenarios] Cobertura incompleta: %d de %d ferramentas.\n", $payload['tool_count'], (int) ($config['minimum_coverage']['expected_tool_count'] ?? 0)));
    exit(1);
}
foreach (($config['minimum_coverage']['required_kinds'] ?? []) as $kind) {
    $covered = count(array_unique(array_column(array_filter($scenarios, static fn (array $scenario): bool => $scenario['kind'] === $kind), 'tool_slug')));
    if ($covered !== $payload['tool_count']) {
        fwrite(STDERR, sprintf("[E2E Scenarios] Cobertura [%s] incompleta: %d de %d ferramentas.\n", $kind, $covered, $payload['tool_count']));
        exit(1);
    }
}

if ($command === 'check') {
    fwrite(STDOUT, sprintf("[E2E Scenarios] %d cenários válidos cobrindo %d ferramentas (válido + inválido).\n", $payload['scenario_count'], $payload['tool_count']));
    exit(0);
}

$output = $root.'/'.ltrim((string) ($config['runtime']['scenario_manifest'] ?? 'storage/app/e2e/runtime/tool-scenarios.json'), '/');
if (! is_dir(dirname($output)) && ! mkdir(dirname($output), 0775, true) && ! is_dir(dirname($output))) {
    fwrite(STDERR, "[E2E Scenarios] Não foi possível criar o diretório de runtime.\n");
    exit(1);
}
file_put_contents($output, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL, LOCK_EX);
fwrite(STDOUT, sprintf("[E2E Scenarios] Manifesto com %d cenários gerado em [%s].\n", count($scenarios), $output));
