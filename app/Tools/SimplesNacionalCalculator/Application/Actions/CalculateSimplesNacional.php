<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Data\SimplesNacionalResult;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;

final readonly class CalculateSimplesNacional
{
    public function __construct(private SimplesNacionalCalculator $calculator) {}

    /** @param array{annex: string, rbt12: string, monthly_revenue: string} $input */
    public function execute(array $input): SimplesNacionalResult
    {
        return $this->calculator->calculate(
            annex: TaxAnnex::from($input['annex']),
            rbt12: Money::fromDecimal($input['rbt12']),
            monthlyRevenue: Money::fromDecimal($input['monthly_revenue']),
        );
    }
}
