<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\ProfitDistributionCalculator\Domain\Data\PartnerProfitShare;
use App\Tools\ProfitDistributionCalculator\Domain\Data\ProfitDistributionInput;
use App\Tools\ProfitDistributionCalculator\Domain\Enums\ProfitDistributionCriterion;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $partnerLabel,
        public string $ownershipPercentage,
        public string $accountingProfit,
        public string $accumulatedLosses = '0',
        public string $reservesAndUnavailableAmounts = '0',
        public string $adjustments = '0',
        public string $priorDistributions = '0',
        public string $intendedDistribution = '0',
    ) {}

    public function toDomain(): ProfitDistributionInput
    {
        return new ProfitDistributionInput(
            accountingProfit: Money::fromDecimal($this->accountingProfit),
            accumulatedLosses: Money::fromDecimal($this->accumulatedLosses),
            reservesAndUnavailableAmounts: Money::fromDecimal($this->reservesAndUnavailableAmounts),
            adjustments: Money::fromDecimal($this->adjustments),
            priorDistributions: Money::fromDecimal($this->priorDistributions),
            criterion: ProfitDistributionCriterion::Proportional,
            partners: [new PartnerProfitShare(
                key: 'partner-1',
                ownershipPercentage: Percentage::fromString($this->ownershipPercentage),
                label: trim($this->partnerLabel) !== '' ? trim($this->partnerLabel) : 'Sócio',
            )],
            intendedDistribution: Money::fromDecimal($this->intendedDistribution),
        );
    }

    public function toArray(): array
    {
        return [
            'partner_label' => $this->partnerLabel,
            'ownership_percentage' => $this->ownershipPercentage,
            'accounting_profit' => $this->accountingProfit,
            'accumulated_losses' => $this->accumulatedLosses,
            'reserves_and_unavailable_amounts' => $this->reservesAndUnavailableAmounts,
            'adjustments' => $this->adjustments,
            'prior_distributions' => $this->priorDistributions,
            'intended_distribution' => $this->intendedDistribution,
        ];
    }
}
