<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Quality\E2E\Data\ToolDownloadExpectation;
use App\Core\Quality\E2E\Data\ToolScenario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class E2EDownloadValidationContractTest extends TestCase
{
    #[Test]
    public function download_contract_is_declared_and_runner_files_exist(): void
    {
        $config = require base_path('config/e2e_scenarios.php');
        $downloads = [];

        foreach ($config['tools'] as $scenarios) {
            foreach ($scenarios as $scenario) {
                self::assertInstanceOf(ToolScenario::class, $scenario);
                foreach ($scenario->downloads as $download) {
                    self::assertInstanceOf(ToolDownloadExpectation::class, $download);
                    $downloads[] = $download;
                }
            }
        }

        self::assertGreaterThanOrEqual(2, count($downloads));
        self::assertFileExists(base_path('tests/Browser/playwright/helpers/download-validator.ts'));
        self::assertFileExists(base_path('tests/Browser/playwright/tool-downloads.spec.ts'));
        self::assertFileExists(base_path('scripts/e2e-downloads.php'));
    }

    #[Test]
    public function xlsx_pilot_requires_real_ooxml_entries(): void
    {
        $config = require base_path('config/e2e_scenarios.php');
        $scenario = $config['tools']['custo-funcionario-clt'][0];
        $xlsx = collect($scenario->downloads)->firstWhere('format', 'xlsx');

        self::assertInstanceOf(ToolDownloadExpectation::class, $xlsx);
        self::assertContains('[Content_Types].xml', $xlsx->requiredEntries);
        self::assertContains('xl/workbook.xml', $xlsx->requiredEntries);
    }
}
