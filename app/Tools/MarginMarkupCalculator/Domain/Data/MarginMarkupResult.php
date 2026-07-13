<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;

final readonly class MarginMarkupResult
{
    public function __construct(
        public Money $totalCost,
        public Money $salePrice,
        public Money $profit,
        public Percentage $margin,
        public Percentage $markup,
        public string $ruleVersion,
    ) {}

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'total_cost' => $this->totalCost->formatPtBr(),
            'sale_price' => $this->salePrice->formatPtBr(),
            'profit' => $this->profit->formatPtBr(),
            'margin' => $this->margin->toDecimalString().'%',
            'markup' => $this->markup->toDecimalString().'%',
            'rule_version' => $this->ruleVersion,
        ];
    }
}
