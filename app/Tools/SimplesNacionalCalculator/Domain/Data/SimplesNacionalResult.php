<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;

final readonly class SimplesNacionalResult
{
    public function __construct(
        public TaxAnnex $annex,
        public Money $rbt12,
        public Money $monthlyRevenue,
        public TaxBracket $bracket,
        public Percentage $effectiveRate,
        public Money $estimatedDas,
        public string $ruleVersion,
        public string $ruleValidFrom,
    ) {}

    /** @return array<string, int|string> */
    public function toArray(): array
    {
        return [
            'annex' => $this->annex->value,
            'annex_label' => $this->annex->label(),
            'rbt12' => $this->rbt12->formatPtBr(),
            'monthly_revenue' => $this->monthlyRevenue->formatPtBr(),
            'bracket' => $this->bracket->number,
            'bracket_from' => $this->bracket->revenueFrom->formatPtBr(),
            'bracket_until' => $this->bracket->revenueUntil->formatPtBr(),
            'nominal_rate' => $this->bracket->nominalRate->toDecimalString().'%',
            'deduction' => $this->bracket->deduction->formatPtBr(),
            'effective_rate' => $this->effectiveRate->toDecimalString().'%',
            'estimated_das' => $this->estimatedDas->formatPtBr(),
            'formula' => '[(RBT12 × alíquota nominal) − parcela a deduzir] ÷ RBT12',
            'rule_version' => $this->ruleVersion,
            'rule_valid_from' => $this->ruleValidFrom,
        ];
    }
}
