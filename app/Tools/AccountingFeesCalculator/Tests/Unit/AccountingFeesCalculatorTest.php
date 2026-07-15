<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\AccountingFeesCalculator\Domain\Calculators\AccountingFeesCalculator;
use App\Tools\AccountingFeesCalculator\Domain\Enums\BusinessSegment;
use App\Tools\AccountingFeesCalculator\Domain\Enums\OperationalComplexity;
use App\Tools\AccountingFeesCalculator\Domain\Enums\TaxRegime;
use PHPUnit\Framework\TestCase;

final class AccountingFeesCalculatorTest extends TestCase
{
    public function test_calculates_recommended_fee_and_breakdown(): void
    {
        $result = (new AccountingFeesCalculator)->calculate(
            monthlyRevenue: Money::fromDecimal('100.000,00'),
            employees: 5,
            partners: 2,
            monthlyInvoices: 120,
            monthlyBankTransactions: 250,
            taxRegime: TaxRegime::SimplesNacional,
            segment: BusinessSegment::Commerce,
            complexity: OperationalComplexity::Medium,
        );

        self::assertSame('R$ 1.678,43', $result->minimumFee->formatPtBr());
        self::assertSame('R$ 1.930,19', $result->recommendedFee->formatPtBr());
        self::assertSame('1.0.0', $result->ruleVersion);
        self::assertCount(6, $result->breakdown);
        self::assertSame(46, $result->complexityScore);
        self::assertNotEmpty($result->recommendations);
        self::assertSame(47, $result->breakdown[0]['percentage']);
        self::assertSame('Comércio', $result->appliedFactors[0]['label']);
    }

    public function test_requires_at_least_one_partner_or_owner(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Informe pelo menos um sócio ou titular.');

        (new AccountingFeesCalculator)->calculate(
            monthlyRevenue: Money::fromDecimal('10.000,00'),
            employees: 0,
            partners: 0,
            monthlyInvoices: 0,
            monthlyBankTransactions: 0,
            taxRegime: TaxRegime::Mei,
            segment: BusinessSegment::Services,
            complexity: OperationalComplexity::Low,
        );
    }
}
