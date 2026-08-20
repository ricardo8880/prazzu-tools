<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Enums\ToolStatus;
use PHPUnit\Framework\TestCase;

final class OfficialToolMaturityTest extends TestCase
{
    /** @var array<int, string> */
    private const PROMOTED_IN_LOT_1 = [
        'DifalIcmsCalculator',
        'DigitalCertificateAnalyzer',
        'EcadRoyaltySimulator',
        'IcmsStCalculator',
        'InvoiceWithholdingCalculator',
        'NetSalaryCalculator',
        'OvertimeCalculator',
    ];

    public function test_no_official_tool_is_left_in_draft(): void
    {
        foreach ($this->officialModules() as $moduleName) {
            $module = $this->module($moduleName);
            self::assertNotSame(
                ToolStatus::Draft,
                $module->manifest()->status,
                "A ferramenta oficial [{$moduleName}] não pode permanecer em draft.",
            );
        }
    }

    public function test_lot_one_promotions_are_active(): void
    {
        foreach (self::PROMOTED_IN_LOT_1 as $moduleName) {
            self::assertSame(
                ToolStatus::Active,
                $this->module($moduleName)->manifest()->status,
                "A promoção de maturidade de [{$moduleName}] regrediu.",
            );
        }
    }

    public function test_active_tools_under_current_quality_framework_have_complete_evidence(): void
    {
        $root = dirname(__DIR__, 2);

        foreach ($this->officialModules() as $moduleName) {
            $module = $this->module($moduleName);

            if ($module->manifest()->status !== ToolStatus::Active) {
                continue;
            }


            $moduleRoot = $root.'/app/Tools/'.$moduleName;
            $qualityPath = $moduleRoot.'/QUALITY.md';

            self::assertFileExists($qualityPath, "A ferramenta ativa [{$moduleName}] precisa documentar sua qualidade.");
            self::assertFileExists($moduleRoot.'/Quality/RiskProfile.php', "A ferramenta ativa [{$moduleName}] precisa declarar RiskProfile.");
            self::assertFileExists($moduleRoot.'/Tests/Fixtures/GoldenCases.php', "A ferramenta ativa [{$moduleName}] precisa possuir golden cases.");
            self::assertFileExists($moduleRoot.'/Tests/Unit/ToolQualityContractTest.php', "A ferramenta ativa [{$moduleName}] precisa possuir gate de qualidade.");

            $quality = (string) file_get_contents($qualityPath);
            self::assertDoesNotMatchRegularExpression(
                '/^- \[ \] /m',
                $quality,
                "A ferramenta ativa [{$moduleName}] possui pendência aberta em QUALITY.md.",
            );
        }
    }

    /** @return array<int, string> */
    private function officialModules(): array
    {
        $inventory = require dirname(__DIR__, 2).'/config/product_tools.php';

        return array_values(array_column($inventory['official'], 'module'));
    }

    private function module(string $moduleName): ToolModule
    {
        $class = 'App\\Tools\\'.$moduleName.'\\Tool';
        $module = new $class;

        self::assertInstanceOf(ToolModule::class, $module);

        return $module;
    }
}
