<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Application\Data;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\PartnerProfitShare;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProfitDistributionInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProLaboreInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums\CompanyRegime;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums\ProfitDistributionCriterion;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $competence,
        public string $companyRegime,
        public string $partnerLabel,
        public string $grossProLabore,
        public int $dependents = 0,
        public string $otherOfficialSocialSecurity = '0',
        public string $ownershipPercentage = '100',
        public string $accountingProfit = '0',
        public string $accumulatedLosses = '0',
        public string $reservesAndUnavailableAmounts = '0',
        public string $adjustments = '0',
        public string $priorDistributions = '0',
        public string $intendedDistribution = '0',
    ) {}

    public function toProLaboreDomain(): ProLaboreInput
    {
        return new ProLaboreInput(
            competence: Competence::fromString($this->competence),
            companyRegime: CompanyRegime::fromInput($this->companyRegime),
            grossAmount: Money::fromDecimal($this->grossProLabore),
            dependents: $this->dependents,
            otherOfficialSocialSecurity: Money::fromDecimal($this->otherOfficialSocialSecurity),
        );
    }

    public function toProfitDistributionDomain(): ProfitDistributionInput
    {
        return new ProfitDistributionInput(
            accountingProfit: Money::fromDecimal($this->accountingProfit),
            accumulatedLosses: Money::fromDecimal($this->accumulatedLosses),
            reservesAndUnavailableAmounts: Money::fromDecimal($this->reservesAndUnavailableAmounts),
            adjustments: Money::fromDecimal($this->adjustments),
            priorDistributions: Money::fromDecimal($this->priorDistributions),
            criterion: ProfitDistributionCriterion::Proportional,
            partners: [
                new PartnerProfitShare(
                    key: 'partner-1',
                    ownershipPercentage: Percentage::fromString($this->ownershipPercentage),
                    label: trim($this->partnerLabel) !== '' ? trim($this->partnerLabel) : 'Sócio',
                ),
            ],
            intendedDistribution: Money::fromDecimal($this->intendedDistribution),
        );
    }

    public function toArray(): array
    {
        return [
            'competence' => $this->competence,
            'company_regime' => $this->companyRegime,
            'partner_label' => $this->partnerLabel,
            'gross_pro_labore' => $this->grossProLabore,
            'dependents' => $this->dependents,
            'other_official_social_security' => $this->otherOfficialSocialSecurity,
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
