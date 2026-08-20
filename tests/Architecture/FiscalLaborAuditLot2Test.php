<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Enums\ToolStatus;
use PHPUnit\Framework\TestCase;

final class FiscalLaborAuditLot2Test extends TestCase
{
    /** @var list<string> */
    private const BETA_UNTIL_OPEN_QUALITY_DEBT_IS_CLOSED = [
        'ProLaboreProfitDistributionCalculator',
        'ReceiptIssuer',
        'TaxRegimeComparator',
        'VacationCalculator',
    ];

    public function test_tools_with_known_open_quality_debt_do_not_return_to_active_silently(): void
    {
        $root = dirname(__DIR__, 2);

        foreach (self::BETA_UNTIL_OPEN_QUALITY_DEBT_IS_CLOSED as $moduleName) {
            $class = 'App\\Tools\\'.$moduleName.'\\Tool';
            $module = new $class;
            self::assertInstanceOf(ToolModule::class, $module);
            self::assertSame(ToolStatus::Beta, $module->manifest()->status);

            $quality = $root.'/app/Tools/'.$moduleName.'/QUALITY.md';
            self::assertFileExists($quality);
            self::assertMatchesRegularExpression(
                '/^- \[ \] /m',
                (string) file_get_contents($quality),
                "[{$moduleName}] só deve ser promovido quando sua dívida documentada de qualidade for realmente encerrada.",
            );
        }
    }

    public function test_tax_reform_documents_the_2026_test_year_compensation_rule(): void
    {
        $root = dirname(__DIR__, 2).'/app/Tools/TaxReformSimulator';
        $normative = (string) file_get_contents($root.'/docs/NORMATIVE_RULES.md');
        $calculator = (string) file_get_contents($root.'/Domain/Services/Calculator.php');

        self::assertStringContainsString('não podem ser simplesmente somados', $normative);
        self::assertStringContainsString('transitionOffset', $calculator);
        self::assertStringContainsString('\$input->year === 2026', $calculator);
    }
}
