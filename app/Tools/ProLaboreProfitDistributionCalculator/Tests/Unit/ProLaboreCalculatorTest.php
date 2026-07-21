<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Tests\Unit;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProLaboreInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums\CompanyRegime;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services\ProLaboreCalculator;
use PHPUnit\Framework\TestCase;

final class ProLaboreCalculatorTest extends TestCase
{
    public function test_2026_partial_irrf_reduction_uses_gross_income(): void
    {
        $result = (new ProLaboreCalculator)->calculate(new ProLaboreInput(
            competence: new Competence(2026, 1),
            companyRegime: CompanyRegime::ActualProfit,
            grossAmount: Money::fromMinor(600000),
        ));

        self::assertSame(720000, $result->companyTotalCost->minorAmount());
        self::assertGreaterThan(0, $result->irrfReduction->minorAmount());
        self::assertLessThan($result->irrfBeforeReduction->minorAmount(), $result->irrfWithheld->minorAmount());
    }

    public function test_legal_deductions_are_used_when_greater_than_simplified(): void
    {
        $result = (new ProLaboreCalculator)->calculate(new ProLaboreInput(
            competence: new Competence(2026, 1),
            companyRegime: CompanyRegime::PresumedProfit,
            grossAmount: Money::fromMinor(900000),
            dependents: 4,
        ));

        self::assertSame('legal', $result->irrfDeductionMethod);
    }
}
