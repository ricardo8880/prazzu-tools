<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Tests\Unit;

use App\Tools\TaxReformSimulator\Application\Data\CalculationInput;
use App\Tools\TaxReformSimulator\Domain\Rules\ConsumptionTaxTransitionRule;
use App\Tools\TaxReformSimulator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_2026_test_rates_do_not_inflate_the_informed_legacy_burden(): void
    {
        $result = (new Calculator(new ConsumptionTaxTransitionRule))->calculate(
            new CalculationInput('100000', '9.25', '18', '9', '18', '0', 2026),
        );

        self::assertSame('0.9', $result->details['cbs_rate']);
        self::assertSame('0.1', $result->details['ibs_rate']);
        self::assertSame(100000, $result->details['transition_offset_minor']);
        self::assertSame('R$ 27.250,00', $result->summary[0]->value);
        self::assertSame('R$ 27.250,00', $result->summary[1]->value);
        self::assertSame('R$ 0,00', $result->summary[2]->value);
        self::assertSame('R$ 1.000,00', $result->summary[3]->value);
    }

    public function test_2026_approximate_credits_reduce_test_taxes_and_the_corresponding_offset(): void
    {
        $result = (new Calculator(new ConsumptionTaxTransitionRule))->calculate(
            new CalculationInput('100000', '9.25', '18', '9', '18', '50', 2026),
        );

        self::assertSame(50000, $result->details['transition_offset_minor']);
        self::assertSame('R$ 27.250,00', $result->summary[1]->value);
        self::assertSame('R$ 500,00', $result->summary[3]->value);
    }

    public function test_2029_uses_ten_percent_of_the_ibs_reference_and_ninety_percent_of_legacy_subnational(): void
    {
        $result = (new Calculator(new ConsumptionTaxTransitionRule))->calculate(
            new CalculationInput('100000', '9.25', '18', '9', '18', '0', 2029),
        );

        self::assertSame('9', $result->details['cbs_rate']);
        self::assertSame('1.8', $result->details['ibs_rate']);
        self::assertSame(0, $result->details['legacy_federal_remaining_percent']);
        self::assertSame(90, $result->details['legacy_subnational_remaining_percent']);
    }
    public function test_result_exposes_versioned_normative_snapshot_for_the_selected_transition_year(): void
    {
        $result = (new Calculator(new ConsumptionTaxTransitionRule))->calculate(
            new CalculationInput('100000', '9.25', '18', '9', '18', '0', 2029),
        );

        $memory = $result->calculationMemory;
        self::assertNotNull($memory);
        self::assertCount(1, $memory->normativeRules);

        $snapshot = $memory->normativeRules[0]->toArray();
        self::assertSame(ConsumptionTaxTransitionRule::IDENTIFIER, $snapshot['identifier']);
        self::assertSame(ConsumptionTaxTransitionRule::VERSION, $snapshot['version']);
        self::assertSame('2029-01-01', $snapshot['reference_date']);
        self::assertSame('2026-08-19', $snapshot['verified_at']);
        self::assertCount(3, $snapshot['references']);
    }

}
