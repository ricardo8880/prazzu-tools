<?php

declare(strict_types=1);

const EXIT_USAGE = 64;
const EXIT_INVALID = 65;

$command = $argv[1] ?? null;

if (! in_array($command, ['summarize', 'compare'], true)) {
    fwrite(STDERR, "Uso:\n  php scripts/e2e-report-history.php summarize <saida.json> <resultado1.json> [resultado2.json ...]\n  php scripts/e2e-report-history.php compare <atual.json> <baseline.json> <saida.json>\n");
    exit(EXIT_USAGE);
}

if ($command === 'summarize') {
    $output = $argv[2] ?? null;
    $inputs = array_slice($argv, 3);

    if (! $output || $inputs === []) {
        fwrite(STDERR, "Informe a saída e ao menos um resultado Playwright.\n");
        exit(EXIT_USAGE);
    }

    $summary = summarize($inputs);
    writeJson($output, $summary);
    printf("Resumo E2E: %d testes, %d falhas, %d ignorados.\n", $summary['totals']['tests'], $summary['totals']['failed'], $summary['totals']['skipped']);
    exit(0);
}

$currentPath = $argv[2] ?? null;
$baselinePath = $argv[3] ?? null;
$outputPath = $argv[4] ?? null;

if (! $currentPath || ! $baselinePath || ! $outputPath) {
    fwrite(STDERR, "Informe resumo atual, baseline e arquivo de saída.\n");
    exit(EXIT_USAGE);
}

$current = readJson($currentPath);
$baseline = is_file($baselinePath) ? readJson($baselinePath) : ['failures' => []];
$currentFailures = indexFailures($current['failures'] ?? []);
$baselineFailures = indexFailures($baseline['failures'] ?? []);

$new = array_values(array_diff_key($currentFailures, $baselineFailures));
$known = array_values(array_intersect_key($currentFailures, $baselineFailures));
$resolved = array_values(array_diff_key($baselineFailures, $currentFailures));

$comparison = [
    'schema_version' => 1,
    'generated_at' => gmdate(DATE_ATOM),
    'current_totals' => $current['totals'] ?? [],
    'new_failures' => $new,
    'known_failures' => $known,
    'resolved_failures' => $resolved,
    'counts' => [
        'new' => count($new),
        'known' => count($known),
        'resolved' => count($resolved),
    ],
];

writeJson($outputPath, $comparison);
printf("Comparação E2E: %d novas, %d conhecidas, %d resolvidas.\n", count($new), count($known), count($resolved));
exit($new === [] ? 0 : 1);

/** @param list<string> $paths */
function summarize(array $paths): array
{
    $tests = 0;
    $passed = 0;
    $failed = 0;
    $skipped = 0;
    $flaky = 0;
    $duration = 0.0;
    $failures = [];

    foreach ($paths as $path) {
        foreach (expandPath($path) as $resolvedPath) {
            $report = readJson($resolvedPath);
            $stats = $report['stats'] ?? [];
            $tests += (int) (($stats['expected'] ?? 0) + ($stats['unexpected'] ?? 0) + ($stats['skipped'] ?? 0) + ($stats['flaky'] ?? 0));
            $passed += (int) (($stats['expected'] ?? 0) + ($stats['flaky'] ?? 0));
            $flaky += (int) ($stats['flaky'] ?? 0);
            $failed += (int) ($stats['unexpected'] ?? 0);
            $skipped += (int) ($stats['skipped'] ?? 0);
            $duration += (float) ($stats['duration'] ?? 0);
            collectFailures($report['suites'] ?? [], [], $resolvedPath, $failures);
        }
    }

    usort($failures, static fn (array $a, array $b): int => strcmp($a['fingerprint'], $b['fingerprint']));

    return [
        'schema_version' => 1,
        'generated_at' => gmdate(DATE_ATOM),
        'totals' => compact('tests', 'passed', 'failed', 'skipped', 'flaky', 'duration'),
        'failures' => $failures,
    ];
}

/** @return list<string> */
function expandPath(string $path): array
{
    $matches = glob($path, GLOB_BRACE);
    if ($matches === false || $matches === []) {
        if (! is_file($path)) {
            throw new RuntimeException("Resultado não encontrado: {$path}");
        }
        return [$path];
    }
    return array_values(array_filter($matches, 'is_file'));
}

/** @param array<int, mixed> $suites @param list<string> $parents @param list<array<string, mixed>> $failures */
function collectFailures(array $suites, array $parents, string $source, array &$failures): void
{
    foreach ($suites as $suite) {
        if (! is_array($suite)) {
            continue;
        }
        $title = trim((string) ($suite['title'] ?? ''));
        $path = $title === '' ? $parents : [...$parents, $title];
        collectFailures($suite['suites'] ?? [], $path, $source, $failures);

        foreach ($suite['specs'] ?? [] as $spec) {
            if (! is_array($spec)) {
                continue;
            }
            foreach ($spec['tests'] ?? [] as $test) {
                if (! is_array($test)) {
                    continue;
                }
                $results = $test['results'] ?? [];
                $last = is_array($results) && $results !== [] ? $results[array_key_last($results)] : [];
                $status = (string) ($last['status'] ?? $test['status'] ?? '');
                if (! in_array($status, ['failed', 'timedOut', 'interrupted', 'unexpected'], true)) {
                    continue;
                }

                $specTitle = (string) ($spec['title'] ?? 'teste sem título');
                $file = (string) ($spec['file'] ?? $suite['file'] ?? 'arquivo desconhecido');
                $project = (string) ($test['projectName'] ?? 'projeto padrão');
                $fingerprint = hash('sha256', implode('|', [$project, $file, implode(' > ', $path), $specTitle]));
                $error = $last['error']['message'] ?? $last['errors'][0]['message'] ?? null;

                $failures[] = [
                    'fingerprint' => $fingerprint,
                    'project' => $project,
                    'file' => $file,
                    'title' => trim(implode(' > ', [...$path, $specTitle]), ' >'),
                    'status' => $status,
                    'error' => is_string($error) ? truncate(stripAnsi($error), 2000) : null,
                    'source' => basename($source),
                ];
            }
        }
    }
}

/** @param list<array<string, mixed>> $failures @return array<string, array<string, mixed>> */
function indexFailures(array $failures): array
{
    $indexed = [];
    foreach ($failures as $failure) {
        if (is_array($failure) && isset($failure['fingerprint'])) {
            $indexed[(string) $failure['fingerprint']] = $failure;
        }
    }
    return $indexed;
}

function truncate(string $value, int $length): string
{
    return function_exists('mb_substr') ? mb_substr($value, 0, $length) : substr($value, 0, $length);
}

function stripAnsi(string $value): string
{
    return preg_replace('/\x1B(?:[@-Z\\-_]|\[[0-?]*[ -\/]*[@-~])/', '', $value) ?? $value;
}

/** @return array<string, mixed> */
function readJson(string $path): array
{
    if (! is_file($path)) {
        throw new RuntimeException("Arquivo JSON não encontrado: {$path}");
    }
    $decoded = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
    if (! is_array($decoded)) {
        throw new RuntimeException("JSON inválido: {$path}");
    }
    return $decoded;
}

/** @param array<string, mixed> $data */
function writeJson(string $path, array $data): void
{
    $directory = dirname($path);
    if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
        throw new RuntimeException("Não foi possível criar {$directory}");
    }
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);
}
