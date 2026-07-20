<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\SimplesNacionalCalculator\Application\Calculators\StandardSimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Application\Data\SimplesNacionalCalculationInput;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Domain\Rules\SimplesNacionalTaxTable;
use PHPUnit\Framework\TestCase;

final class StandardSimplesNacionalCalculatorTest extends TestCase
{
    public function test_it_exposes_the_simples_result_through_the_shared_contract(): void
    {
        $calculator = new StandardSimplesNacionalCalculator(
            new SimplesNacionalCalculator(new SimplesNacionalTaxTable),
        );

        $result = $calculator->calculate(new SimplesNacionalCalculationInput(
            annex: TaxAnnex::I,
            rbt12: Money::fromDecimal('300000.00'),
            monthlyRevenue: Money::fromDecimal('25000.00'),
        ));

        self::assertSame('calculadora-simples-nacional', $result->toolSlug);
        self::assertSame('1.0.0', $result->schemaVersion);
        self::assertSame('R$ 1.330,00', $result->details['estimated_das']);
        self::assertSame('company-tax-snapshot:v1', $result->integrationPayload?->contractKey());
        self::assertSame('25000.00', $result->integrationPayload?->data['monthly_revenue']);
        self::assertSame('1330.00', $result->integrationPayload?->data['estimated_das']);
    }

    public function test_input_can_be_created_from_validated_form_data(): void
    {
        $input = SimplesNacionalCalculationInput::fromArray([
            'annex' => 'III',
            'rbt12' => '120000.00',
            'monthly_revenue' => '10000.00',
        ]);

        self::assertSame([
            'annex' => 'III',
            'rbt12' => '120000.00',
            'monthly_revenue' => '10000.00',
        ], $input->toArray());
    }
}
