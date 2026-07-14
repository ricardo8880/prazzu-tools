<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Tools\SimplesNacionalCalculator\Application\Actions\CalculateFactorR;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\FactorRCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use PHPUnit\Framework\TestCase;

final class CalculateFactorRTest extends TestCase
{
    public function test_converts_input_and_calculates_factor_r(): void
    {
        $result = (new CalculateFactorR(new FactorRCalculator))->execute([
            'payroll_12' => 'R$ 42.000,00',
            'rbt12' => 'R$ 150.000,00',
        ]);

        self::assertSame('28', $result->factorR->toDecimalString());
        self::assertSame(TaxAnnex::III, $result->applicableAnnex);
        self::assertSame(FactorRCalculator::RULE_VERSION, $result->ruleVersion);
    }
}
