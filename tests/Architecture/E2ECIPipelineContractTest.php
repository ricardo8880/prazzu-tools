<?php

declare(strict_types=1);

namespace Tests\Architecture;

use Tests\TestCase;

final class E2ECIPipelineContractTest extends TestCase
{
    public function test_ci_parallelism_and_executive_reporting_contract_is_preserved(): void
    {
        $workflow = (string) file_get_contents(base_path('.github/workflows/quality.yml'));
        $package = json_decode(
            (string) file_get_contents(base_path('package.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
        $composer = json_decode(
            (string) file_get_contents(base_path('composer.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        self::assertStringContainsString('Smoke de commit', $workflow);
        self::assertStringContainsString('pull_request:', $workflow);
        self::assertStringContainsString("tags: ['v*']", $workflow);
        self::assertStringContainsString('matrix:', $workflow);
        self::assertStringContainsString('shard: [1, 2, 3, 4]', $workflow);
        self::assertStringContainsString('actions/cache@v4', $workflow);
        self::assertStringContainsString('actions/upload-artifact@v4', $workflow);
        self::assertStringContainsString('Relatório executivo', $workflow);
        self::assertStringContainsString('e2e-report-history.php compare', $workflow);

        self::assertArrayHasKey('e2e:test:ci', $package['scripts']);
        self::assertArrayHasKey('e2e:report:summarize', $package['scripts']);
        self::assertArrayHasKey('e2e:ci:smoke', $composer['scripts']);
        self::assertArrayHasKey('e2e:ci:complete', $composer['scripts']);
        self::assertArrayHasKey('e2e:report:summary', $composer['scripts']);
        self::assertFileExists(base_path('scripts/e2e-report-history.php'));
    }
}
