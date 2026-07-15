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

    /** @param array<string, string|null> $input */
    public function execute(array $input): MarginMarkupResult
    {
        return $this->calculator->calculate(
            baseCost: Money::fromDecimal($input['base_cost']),
            additionalCosts: Money::fromDecimal($this->valueOrZero($input['additional_costs'] ?? null)),
            freightCost: Money::fromDecimal($this->valueOrZero($input['freight_cost'] ?? null)),
            packagingCost: Money::fromDecimal($this->valueOrZero($input['packaging_cost'] ?? null)),
            fixedExpenses: Money::fromDecimal($this->valueOrZero($input['fixed_expenses'] ?? null)),
            desiredMargin: Percentage::fromString($input['desired_margin']),
            taxes: Percentage::fromString($this->valueOrZero($input['taxes_percentage'] ?? null)),
            commission: Percentage::fromString($this->valueOrZero($input['commission_percentage'] ?? null)),
            cardFees: Percentage::fromString($this->valueOrZero($input['card_fees_percentage'] ?? null)),
            marketplaceFees: Percentage::fromString($this->valueOrZero($input['marketplace_fees_percentage'] ?? null)),
        );
    }

    private function valueOrZero(?string $value): string
    {
        return $value === null || trim($value) === '' ? '0' : $value;
    }
}
