<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\ProLaboreSimulator\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(private ?ProLaboreCalculator $calculator = null) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com o simulador de pró-labore.');
        }

        $result = ($this->calculator ?? new ProLaboreCalculator)->calculate($input->toDomain());

        return new ToolCalculationResult(
            toolSlug: 'simulador-pro-labore-ideal',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('gross', 'Pró-labore bruto', $result->grossAmount->formatPtBr()),
                new ToolCalculationSummaryItem('inss', 'INSS retido', $result->socialSecurityWithheld->formatPtBr()),
                new ToolCalculationSummaryItem('irrf', 'IRRF retido', $result->irrfWithheld->formatPtBr()),
                new ToolCalculationSummaryItem('net', 'Pró-labore líquido', $result->netAmount->formatPtBr()),
                new ToolCalculationSummaryItem('company_cost', 'Custo para a empresa', $result->companyTotalCost->formatPtBr()),
            ],
            details: [
                'input' => $input->toArray(),
                'gross_minor' => $result->grossAmount->minorAmount(),
                'social_security_base_minor' => $result->socialSecurityBase->minorAmount(),
                'inss_withheld_minor' => $result->socialSecurityWithheld->minorAmount(),
                'irrf_base_minor' => $result->irrfBase->minorAmount(),
                'irrf_withheld_minor' => $result->irrfWithheld->minorAmount(),
                'net_minor' => $result->netAmount->minorAmount(),
                'employer_contribution_minor' => $result->employerContribution->minorAmount(),
                'company_total_cost_minor' => $result->companyTotalCost->minorAmount(),
                'irrf_deduction_method' => $result->irrfDeductionMethod,
                'memory' => $result->memory,
                'normative_rules' => $result->normativeRules,
            ],
        );
    }
}
