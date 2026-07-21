<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Application\Data;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\PartnerProfitShare;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProfitDistributionInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProLaboreInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums\CompanyRegime;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums\ProfitDistributionCriterion;

final readonly class SimulationInput
{
    /** @param array<int, array<string, mixed>> $scenarios */
    public function __construct(public array $scenarios) {}

    /** @param array<string, mixed> $partner */
    public function proLaboreInput(array $period, array $partner): ProLaboreInput
    {
        return new ProLaboreInput(
            competence: Competence::fromString((string) $period['competence']),
            companyRegime: CompanyRegime::fromInput((string) $period['company_regime']),
            grossAmount: Money::fromDecimal((string) $partner['gross_pro_labore']),
            dependents: (int) ($partner['dependents'] ?? 0),
            otherOfficialSocialSecurity: Money::fromDecimal((string) ($partner['other_official_social_security'] ?? '0')),
        );
    }

    /** @param array<string, mixed> $period */
    public function profitInput(array $period): ProfitDistributionInput
    {
        $partners = [];
        foreach ($period['partners'] as $index => $partner) {
            $partners[] = new PartnerProfitShare(
                key: 'partner-'.($index + 1),
                ownershipPercentage: Percentage::fromString((string) $partner['ownership_percentage']),
                label: trim((string) ($partner['label'] ?? '')) ?: 'Sócio '.($index + 1),
            );
        }

        return new ProfitDistributionInput(
            accountingProfit: Money::fromDecimal((string) $period['accounting_profit']),
            accumulatedLosses: Money::fromDecimal((string) ($period['accumulated_losses'] ?? '0')),
            reservesAndUnavailableAmounts: Money::fromDecimal((string) ($period['reserves_and_unavailable_amounts'] ?? '0')),
            adjustments: Money::fromDecimal((string) ($period['adjustments'] ?? '0')),
            priorDistributions: Money::fromDecimal((string) ($period['prior_distributions'] ?? '0')),
            criterion: ProfitDistributionCriterion::Proportional,
            partners: $partners,
            intendedDistribution: Money::fromDecimal((string) $period['intended_distribution']),
        );
    }
}
