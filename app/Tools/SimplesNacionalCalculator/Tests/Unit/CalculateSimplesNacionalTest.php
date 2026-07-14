<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Tools\SimplesNacionalCalculator\Application\Actions\CalculateSimplesNacional;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Rules\SimplesNacionalTaxTable;
use PHPUnit\Framework\TestCase;

final class CalculateSimplesNacionalTest extends TestCase
{
    public function test_converts_input_and_returns_calculation_result(): void
    {
        $action = new CalculateSimplesNacional(
            new SimplesNacionalCalculator(new SimplesNacionalTaxTable),
        );

        $result = $action->execute([
            'annex' => 'III',
            'rbt12' => '300.000,00',
            'monthly_revenue' => '25.000,00',
        ]);

        self::assertSame('III', $result->annex->value);
        self::assertSame('8.08', $result->effectiveRate->toDecimalString());
        self::assertSame('R$ 2.020,00', $result->estimatedDas->formatPtBr());
    }
}
