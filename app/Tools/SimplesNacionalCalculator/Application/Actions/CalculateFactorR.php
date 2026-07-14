<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\FactorRCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Data\FactorRResult;

final readonly class CalculateFactorR
{
    public function __construct(private FactorRCalculator $calculator) {}

    /** @param array{payroll_12: string, rbt12: string} $input */
    public function execute(array $input): FactorRResult
    {
        return $this->calculator->calculate(
            payroll12: Money::fromDecimal($input['payroll_12']),
            rbt12: Money::fromDecimal($input['rbt12']),
        );
    }
}
