<?php

declare(strict_types=1);

it('mantém o contrato de CI, paralelismo e relatório executivo do lote 11', function (): void {
    $workflow = file_get_contents(base_path('.github/workflows/quality.yml'));
    $package = json_decode(file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);
    $composer = json_decode(file_get_contents(base_path('composer.json')), true, flags: JSON_THROW_ON_ERROR);

    expect($workflow)
        ->toContain('Smoke de commit')
        ->toContain('pull_request:')
        ->toContain("tags: ['v*']")
        ->toContain('matrix:')
        ->toContain('shard: [1, 2, 3, 4]')
        ->toContain('actions/cache@v4')
        ->toContain('actions/upload-artifact@v4')
        ->toContain('Relatório executivo')
        ->toContain('e2e-report-history.php compare');

    expect($package['scripts'])->toHaveKeys(['e2e:test:ci', 'e2e:report:summarize']);
    expect($composer['scripts'])->toHaveKeys(['e2e:ci:smoke', 'e2e:ci:complete', 'e2e:report:summary']);
    expect(base_path('scripts/e2e-report-history.php'))->toBeFile();
});
