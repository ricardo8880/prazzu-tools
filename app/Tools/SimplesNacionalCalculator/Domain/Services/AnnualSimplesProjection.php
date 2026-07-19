<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Data\AnnualProjectionResult;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;

final readonly class AnnualSimplesProjection
{
    public function __construct(private SimplesNacionalCalculator $calculator) {}

    public function project(
        TaxAnnex $annex,
        Money $initialMonthlyRevenue,
        Percentage $monthlyGrowth,
    ): AnnualProjectionResult {
        $revenue = $initialMonthlyRevenue;
        $totalRevenue = Money::zero($revenue->currency());
        $totalDas = Money::zero($revenue->currency());
        $months = [];

        for ($month = 1; $month <= 12; $month++) {
            if ($month > 1) {
                $revenue = $revenue->add($revenue->percentage($monthlyGrowth));
            }

            $result = $this->calculator->calculate(
                annex: $annex,
                rbt12: $revenue->multiply(12),
                monthlyRevenue: $revenue,
            );

            $months[] = $result;
            $totalRevenue = $totalRevenue->add($revenue);
            $totalDas = $totalDas->add($result->estimatedDas);
        }

        return new AnnualProjectionResult($months, $totalRevenue, $totalDas);
    }
}
