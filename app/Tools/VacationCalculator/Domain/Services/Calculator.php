<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\VacationCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(private ?VacationCalculator $vacationCalculator = null) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a ferramenta calculadora-ferias.');
        }

        $result = ($this->vacationCalculator ?? new VacationCalculator)->calculate($input->toDomain());

        return new ToolCalculationResult(
            toolSlug: 'calculadora-ferias',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('entitled_days', 'Dias de direito', $result->entitledDays),
                new ToolCalculationSummaryItem('leave_days', 'Dias de descanso', $result->leaveDays),
                new ToolCalculationSummaryItem('cash_allowance_days', 'Dias convertidos em abono', $result->cashAllowanceDays),
                new ToolCalculationSummaryItem('gross_total', 'Total bruto', $result->grossTotal->formatPtBr()),
                new ToolCalculationSummaryItem('net_total', 'Total líquido estimado', $result->netTotal->formatPtBr()),
            ],
            details: [
                'input' => $input->toArray(),
                'rule_version' => VacationCalculator::RULE_VERSION,
                'remuneration' => [
                    'base_minor' => $result->remunerationBase->minorAmount(),
                    'vacation_minor' => $result->vacationRemuneration->minorAmount(),
                    'vacation_third_minor' => $result->vacationThird->minorAmount(),
                    'cash_allowance_minor' => $result->cashAllowance->minorAmount(),
                    'cash_allowance_third_minor' => $result->cashAllowanceThird->minorAmount(),
                    'gross_total_minor' => $result->grossTotal->minorAmount(),
                    'other_deductions_minor' => $result->otherDeductions->minorAmount(),
                    'net_total_minor' => $result->netTotal->minorAmount(),
                ],
                'periods' => [
                    'acquisition_end_date' => $result->acquisitionEndDate->format('Y-m-d'),
                    'concession_deadline' => $result->concessionDeadline->format('Y-m-d'),
                    'payment_deadline' => $result->paymentDeadline->format('Y-m-d'),
                    'concession_period_overdue' => $result->concessionPeriodOverdue,
                ],
            ],
            warnings: array_map(
                static fn (string $warning): ToolCalculationWarning => new ToolCalculationWarning('domain_attention', $warning),
                $result->warnings,
            ),
        );
    }
}
