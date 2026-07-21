<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Application\Actions;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Tools\ProLaboreProfitDistributionCalculator\Application\Data\CalculationInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services\Calculator;

final readonly class CalculateTool
{
    public function __construct(private Calculator $calculator) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data): ToolCalculationResult
    {
        return $this->calculator->calculate(new CalculationInput(
            competence: (string) $data['competence'],
            companyRegime: (string) $data['company_regime'],
            partnerLabel: (string) ($data['partner_label'] ?? ''),
            grossProLabore: (string) $data['gross_pro_labore'],
            dependents: (int) ($data['dependents'] ?? 0),
            otherOfficialSocialSecurity: (string) ($data['other_official_social_security'] ?? '0'),
            ownershipPercentage: (string) ($data['ownership_percentage'] ?? '100'),
            accountingProfit: (string) ($data['accounting_profit'] ?? '0'),
            accumulatedLosses: (string) ($data['accumulated_losses'] ?? '0'),
            reservesAndUnavailableAmounts: (string) ($data['reserves_and_unavailable_amounts'] ?? '0'),
            adjustments: (string) ($data['adjustments'] ?? '0'),
            priorDistributions: (string) ($data['prior_distributions'] ?? '0'),
            intendedDistribution: (string) ($data['intended_distribution'] ?? '0'),
        ));
    }
}
