<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Tests\Unit;

use App\Tools\ProLaboreProfitDistributionCalculator\Application\Data\CalculationInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_combines_pro_labore_and_profit_distribution_in_application_result(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            competence: '2026-01',
            companyRegime: 'simples_annex_iv',
            partnerLabel: 'Sócio A',
            grossProLabore: '5000,00',
            accountingProfit: '20000,00',
            intendedDistribution: '12000,00',
        ));

        self::assertSame('3.0.0', $result->schemaVersion);
        self::assertSame(55000, $result->details['pro_labore']['inss_withheld_minor']);
        self::assertSame(445000, $result->details['pro_labore']['net_minor']);
        self::assertSame(1200000, $result->details['profit_distribution']['distributed_amount_minor']);
        self::assertSame(1645000, $result->details['partner']['total_received_minor']);
        self::assertSame('Sócio A', $result->details['partner']['label']);
    }

    public function test_it_keeps_the_two_domain_memories_in_the_consolidated_result(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput(
            competence: '2026-06',
            companyRegime: 'simples_outside_annex_iv',
            partnerLabel: '',
            grossProLabore: '10000.00',
            accountingProfit: '10000.00',
            accumulatedLosses: '2000.00',
            intendedDistribution: '5000.00',
        ));

        self::assertNotEmpty($result->details['pro_labore']['memory']);
        self::assertNotEmpty($result->details['profit_distribution']['memory']);
        self::assertNotEmpty($result->details['normative_rules']);
        self::assertSame(300000, $result->details['profit_distribution']['undistributed_balance_minor']);
    }
}
