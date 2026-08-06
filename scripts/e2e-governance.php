<?php

declare(strict_types=1);

const EXIT_USAGE = 64;
const EXIT_INVALID = 65;

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$config = require $root.'/config/e2e_governance.php';
$command = $argv[1] ?? 'check';

if (! in_array($command, ['check', 'dashboard', 'prune'], true)) {
    fwrite(STDERR, "Uso: php scripts/e2e-governance.php [check|dashboard <resumo.json>|prune]\n");
    exit(EXIT_USAGE);
}

if ($command === 'check') {
    $product = require $root.'/config/product_tools.php';
    $scenarios = require $root.'/config/e2e_scenarios.php';
    $official = array_column($product['official'] ?? [], 'slug');
    $declared = array_keys($scenarios['tools'] ?? []);
    sort($official);
    sort($declared);
    $failures = [];

    if (count($official) !== (int) $config['catalog']['expected_tool_count']) {
        $failures[] = 'A quantidade de ferramentas oficiais diverge da governança.';
    }
    if ($official !== $declared) {
        $failures[] = 'Toda ferramenta oficial deve possuir contrato E2E; cenário ausente ou ferramenta órfã detectada.';
    }
    foreach (($scenarios['tools'] ?? []) as $slug => $toolScenarios) {
        $kinds = [];
        foreach ($toolScenarios as $scenario) {
            if (is_object($scenario) && method_exists($scenario, 'toArray')) {
                $data = $scenario->toArray();
                $kinds[] = $data['kind'] ?? null;
            }
        }
        foreach ($config['catalog']['required_scenario_kinds'] as $required) {
            if (! in_array($required, $kinds, true)) $failures[] = "[{$slug}] não possui cenário obrigatório [{$required}].";
        }
    }
    if (($config['exploration']['blocking'] ?? true) !== false) {
        $failures[] = 'O modo exploratório deve permanecer fora do gate bloqueante.';
    }
    if ($failures !== []) {
        foreach ($failures as $failure) fwrite(STDERR, "[E2E Governance] {$failure}\n");
        exit(EXIT_INVALID);
    }
    printf("[E2E Governance] %d ferramentas protegidas por contrato; exploração não bloqueante.\n", count($official));
    exit(0);
}

if ($command === 'dashboard') {
    $summaryPath = $argv[2] ?? null;
    if (! $summaryPath || ! is_file($summaryPath)) {
        fwrite(STDERR, "Informe um resumo executivo JSON existente.\n");
        exit(EXIT_USAGE);
    }
    $summary = json_decode((string) file_get_contents($summaryPath), true, flags: JSON_THROW_ON_ERROR);
    $totals = $summary['totals'] ?? [];
    $tests = max(1, (int) ($totals['tests'] ?? 0));
    $flaky = (int) ($totals['flaky'] ?? 0);
    $skipped = (int) ($totals['skipped'] ?? 0);
    $duration = (float) ($totals['duration'] ?? 0);
    $metrics = [
        'schema_version' => 1,
        'generated_at' => gmdate(DATE_ATOM),
        'coverage' => ['tools' => (int) $config['catalog']['expected_tool_count'], 'required_kinds' => $config['catalog']['required_scenario_kinds']],
        'health' => [
            'tests' => $tests,
            'failed' => (int) ($totals['failed'] ?? 0),
            'flaky' => $flaky,
            'skipped' => $skipped,
            'duration_ms' => $duration,
            'flaky_rate' => $flaky / $tests,
            'skipped_rate' => $skipped / $tests,
        ],
        'thresholds' => $config['health'],
    ];
    $metrics['healthy'] = $metrics['health']['failed'] === 0
        && $metrics['health']['flaky_rate'] <= $config['health']['max_flaky_rate']
        && $metrics['health']['skipped_rate'] <= $config['health']['max_skipped_rate']
        && $duration <= $config['health']['max_suite_duration_ms'];

    $jsonPath = $root.'/'.ltrim($config['paths']['dashboard_json'], '/');
    $htmlPath = $root.'/'.ltrim($config['paths']['dashboard_html'], '/');
    if (! is_dir(dirname($jsonPath))) mkdir(dirname($jsonPath), 0775, true);
    file_put_contents($jsonPath, json_encode($metrics, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL);
    $status = $metrics['healthy'] ? 'SAUDÁVEL' : 'ATENÇÃO';
    $html = '<!doctype html><html lang="pt-BR"><meta charset="utf-8"><title>Saúde E2E</title><body><h1>Saúde da suíte E2E</h1>'
        .'<p>Status: <strong>'.$status.'</strong></p><p>Ferramentas cobertas: '.$metrics['coverage']['tools'].'</p>'
        .'<p>Testes: '.$tests.' | Falhas: '.$metrics['health']['failed'].' | Flaky: '.$flaky.' | Ignorados: '.$skipped.'</p>'
        .'<p>Duração: '.number_format($duration / 1000, 2, ',', '.').' s</p></body></html>';
    file_put_contents($htmlPath, $html);
    printf("[E2E Governance] Painel gerado em [%s] e [%s].\n", $jsonPath, $htmlPath);
    exit(0);
}

foreach ([['path' => $config['paths']['artifact_root'], 'days' => $config['retention']['artifacts_days']], ['path' => $config['paths']['executive_root'], 'days' => $config['retention']['executive_reports_days']]] as $policy) {
    $directory = $root.'/'.ltrim($policy['path'], '/');
    if (! is_dir($directory)) continue;
    $cutoff = time() - ((int) $policy['days'] * 86400);
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($iterator as $item) {
        if ($item->isFile() && $item->getMTime() < $cutoff) @unlink($item->getPathname());
        elseif ($item->isDir()) @rmdir($item->getPathname());
    }
}
fwrite(STDOUT, "[E2E Governance] Política de retenção aplicada.\n");
