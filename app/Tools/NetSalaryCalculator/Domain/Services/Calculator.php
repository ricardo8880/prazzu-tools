<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\NetSalaryCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(private ?NetSalaryCalculator $calculator = null) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a calculadora de salário líquido.');
        }

        $result = ($this->calculator ?? new NetSalaryCalculator)->calculate($input->toDomain());

        return new ToolCalculationResult(
            toolSlug: 'calculadora-salario-liquido',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('gross', 'Proventos totais', $result->totalEarnings->formatPtBr()),
                new ToolCalculationSummaryItem('inss', 'INSS', $result->socialSecurityWithheld->formatPtBr()),
                new ToolCalculationSummaryItem('irrf', 'IRRF', $result->irrfWithheld->formatPtBr()),
                new ToolCalculationSummaryItem('discounts', 'Descontos totais', $result->totalDiscounts->formatPtBr()),
                new ToolCalculationSummaryItem('net', 'Salário líquido', $result->netSalary->formatPtBr()),
            ],
            details: [
                'input' => $input->toArray(),
                'taxable_gross_minor' => $result->taxableGross->minorAmount(),
                'total_earnings_minor' => $result->totalEarnings->minorAmount(),
                'social_security_base_minor' => $result->socialSecurityBase->minorAmount(),
                'inss_minor' => $result->socialSecurityWithheld->minorAmount(),
                'legal_irrf_deductions_minor' => $result->legalIrrfDeductions->minorAmount(),
                'simplified_irrf_deduction_minor' => $result->simplifiedIrrfDeduction->minorAmount(),
                'irrf_deduction_method' => $result->irrfDeductionMethod,
                'irrf_base_minor' => $result->irrfBase->minorAmount(),
                'irrf_before_reduction_minor' => $result->irrfBeforeReduction->minorAmount(),
                'irrf_reduction_minor' => $result->irrfReduction->minorAmount(),
                'irrf_minor' => $result->irrfWithheld->minorAmount(),
                'user_discounts_minor' => $result->userDiscounts->minorAmount(),
                'total_discounts_minor' => $result->totalDiscounts->minorAmount(),
                'net_minor' => $result->netSalary->minorAmount(),
            ],
            warnings: [
                new ToolCalculationWarning(
                    code: 'estimated_payroll_result',
                    message: 'Estimativa para salário mensal CLT regular. Eventos com incidência específica, múltiplos vínculos, férias, 13º e rescisão exigem tratamento próprio.',
                    level: ToolCalculationWarningLevel::Info,
                ),
            ],
            calculationMemory: $result->memory,
        );
    }
}
