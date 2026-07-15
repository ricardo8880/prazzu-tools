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
        public Money $grossProfit,
        public Money $netProfit,
        public Money $taxesAmount,
        public Money $commissionAmount,
        public Money $cardFeesAmount,
        public Money $marketplaceFeesAmount,
        public Percentage $margin,
        public Percentage $markup,
        public string $markupMultiplier,
        public string $ruleVersion,
    ) {}

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'total_cost' => $this->totalCost->formatPtBr(),
            'sale_price' => $this->salePrice->formatPtBr(),
            'gross_profit' => $this->grossProfit->formatPtBr(),
            'net_profit' => $this->netProfit->formatPtBr(),
            'taxes_amount' => $this->taxesAmount->formatPtBr(),
            'commission_amount' => $this->commissionAmount->formatPtBr(),
            'card_fees_amount' => $this->cardFeesAmount->formatPtBr(),
            'marketplace_fees_amount' => $this->marketplaceFeesAmount->formatPtBr(),
            'margin' => $this->margin->toDecimalString().'%',
            'markup' => $this->markup->toDecimalString().'%',
            'markup_multiplier' => $this->markupMultiplier,
            'rule_version' => $this->ruleVersion,
        ];
    }
}
