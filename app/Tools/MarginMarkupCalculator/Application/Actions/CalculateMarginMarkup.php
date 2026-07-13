<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\MarginMarkupCalculator\Domain\Calculators\MarginMarkupCalculator;
use App\Tools\MarginMarkupCalculator\Domain\Data\MarginMarkupResult;

final readonly class CalculateMarginMarkup
{
    public function __construct(private MarginMarkupCalculator $calculator) {}

    /** @param array<string, string> $input */
    public function execute(array $input): MarginMarkupResult
    {
        return $this->calculator->calculate(
            Money::fromDecimal($input['base_cost']),
            Money::fromDecimal($input['additional_costs'] ?? '0'),
            Percentage::fromString($input['desired_margin']),
        );
    }
}
