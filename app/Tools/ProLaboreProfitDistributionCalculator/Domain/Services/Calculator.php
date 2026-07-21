<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\ProLaboreProfitDistributionCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(
        private ?ProLaboreCalculator $proLaboreCalculator = null,
        private ?ProfitDistributionCalculator $profitDistributionCalculator = null,
    ) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a ferramenta calculadora-pro-labore-distribuicao-lucros.');
        }

        $proLabore = ($this->proLaboreCalculator ?? new ProLaboreCalculator)->calculate($input->toProLaboreDomain());
        $profit = ($this->profitDistributionCalculator ?? new ProfitDistributionCalculator)->calculate($input->toProfitDistributionDomain());
        $partnerDistribution = $profit->partners[0]->distributedAmount;
        $totalReceived = $proLabore->netAmount->add($partnerDistribution);

        return new ToolCalculationResult(
            toolSlug: 'calculadora-pro-labore-distribuicao-lucros',
            schemaVersion: '3.0.0',
            summary: [
                new ToolCalculationSummaryItem('net_pro_labore', 'Pró-labore líquido', $proLabore->netAmount->formatPtBr()),
                new ToolCalculationSummaryItem('profit_distribution', 'Lucro distribuído', $partnerDistribution->formatPtBr()),
                new ToolCalculationSummaryItem('partner_total_received', 'Total recebido pelo sócio', $totalReceived->formatPtBr()),
                new ToolCalculationSummaryItem('company_total_cost', 'Custo do pró-labore para a empresa', $proLabore->companyTotalCost->formatPtBr()),
                new ToolCalculationSummaryItem('undistributed_profit', 'Lucro não distribuído', $profit->undistributedBalance->formatPtBr()),
            ],
            details: [
                'input' => $input->toArray(),
                'partner' => [
                    'label' => $profit->partners[0]->label,
                    'ownership_percentage' => $profit->partners[0]->ownershipPercentage->toDecimalString(),
                    'profit_distribution_minor' => $partnerDistribution->minorAmount(),
                    'total_received_minor' => $totalReceived->minorAmount(),
                ],
                'pro_labore' => [
                    'gross_minor' => $proLabore->grossAmount->minorAmount(),
                    'social_security_base_minor' => $proLabore->socialSecurityBase->minorAmount(),
                    'inss_withheld_minor' => $proLabore->socialSecurityWithheld->minorAmount(),
                    'irrf_base_minor' => $proLabore->irrfBase->minorAmount(),
                    'irrf_before_reduction_minor' => $proLabore->irrfBeforeReduction->minorAmount(),
                    'irrf_reduction_minor' => $proLabore->irrfReduction->minorAmount(),
                    'irrf_withheld_minor' => $proLabore->irrfWithheld->minorAmount(),
                    'net_minor' => $proLabore->netAmount->minorAmount(),
                    'employer_contribution_minor' => $proLabore->employerContribution->minorAmount(),
                    'company_total_cost_minor' => $proLabore->companyTotalCost->minorAmount(),
                    'irrf_deduction_method' => $proLabore->irrfDeductionMethod,
                    'memory' => $proLabore->memory,
                ],
                'profit_distribution' => [
                    'accounting_profit_minor' => $profit->accountingProfit->minorAmount(),
                    'maximum_available_profit_minor' => $profit->maximumAvailableProfit->minorAmount(),
                    'distributed_amount_minor' => $profit->distributedAmount->minorAmount(),
                    'undistributed_balance_minor' => $profit->undistributedBalance->minorAmount(),
                    'memory' => $profit->memory,
                    'warnings' => $profit->warnings,
                ],
                'normative_rules' => $proLabore->normativeRules,
                'warnings' => $profit->warnings,
            ],
        );
    }
}
