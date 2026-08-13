<?php

declare(strict_types=1);

namespace App\Tools\LateDasCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Money\Percentage;
use App\Core\Tax\Normative\LateDasRule;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\LateDasCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function __construct(private readonly LateDasRule $rule = new LateDasRule) {}

    public function calculate(ToolCalculationInput $i): ToolCalculationResult
    {
        if (! $i instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }
        $late = $i->paymentDate > $i->dueDate;
        $days = $late ? (int) $i->dueDate->diff($i->paymentDate)->format('%a') : 0;
        $dailyFineBp = intdiv($this->rule->dailyFine()->millionthsOfPercent(), 10_000);
        $maximumFineBp = intdiv($this->rule->maximumFine()->millionthsOfPercent(), 10_000);
        $fineBp = min($maximumFineBp, $days * $dailyFineBp);
        $fineRate = Percentage::fromBasisPoints($fineBp);
        $interestRate = $late ? Percentage::fromString($this->scaledPercent($this->rule->paymentMonthInterest()->millionthsOfPercent() + $i->accumulatedSelic->millionthsOfPercent())) : Percentage::zero();
        $fine = $i->principal->percentage($fineRate);
        $interest = $i->principal->percentage($interestRate);
        $total = $i->principal->add($fine)->add($interest);

        return new ToolCalculationResult(
            'das-em-atraso',
            '1.1.0',
            [
                new ToolCalculationSummaryItem('total', 'DAS atualizado', $total->formatPtBr()),
                new ToolCalculationSummaryItem('fine', 'Multa de mora', $fine->formatPtBr(), $this->basisPoints($fineBp)),
                new ToolCalculationSummaryItem('interest', 'Juros de mora', $interest->formatPtBr(), $interestRate->toDecimalString().'%'),
                new ToolCalculationSummaryItem('days_late', 'Dias em atraso', (string) $days),
            ],
            ['input' => $i->toArray(), 'rule_version' => LateDasRule::VERSION],
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.0.0',
                steps: [
                    new CalculationMemoryStep('days_late', 'Dias em atraso', 'máximo(0, data de pagamento − vencimento)', ['due_date' => $i->dueDate->format('Y-m-d'), 'payment_date' => $i->paymentDate->format('Y-m-d')], $days),
                    new CalculationMemoryStep('fine', 'Multa de mora', 'principal × mínimo(20%; dias × 0,33%)', ['principal' => $i->principal->minorAmount(), 'days' => $days, 'daily_fine_basis_points' => $dailyFineBp, 'maximum_fine_basis_points' => $maximumFineBp], $fine->minorAmount(), 'Arredondamento monetário em centavos.'),
                    new CalculationMemoryStep('interest', 'Juros de mora', 'principal × (Selic acumulada informada + 1% no mês do pagamento)', ['principal' => $i->principal->minorAmount(), 'accumulated_selic' => $i->accumulatedSelic->toDecimalString()], $interest->minorAmount(), 'Arredondamento monetário em centavos.'),
                    new CalculationMemoryStep('total', 'DAS atualizado', 'principal + multa + juros', ['principal' => $i->principal->minorAmount(), 'fine' => $fine->minorAmount(), 'interest' => $interest->minorAmount()], $total->minorAmount()),
                ],
                normativeRules: [$this->rule->snapshot(ReferenceDate::fromDateTime($i->paymentDate))],
                assumptions: ['A Selic acumulada deve ser informada para o período correto; a ferramenta acrescenta 1% referente ao mês do pagamento.', 'O resultado é estimativo e não substitui a guia oficial emitida pelo sistema do Simples Nacional.'],
                isEstimate: true,
            ),
        );
    }

    private function scaledPercent(int $scaled): string
    {
        $w = intdiv($scaled, 1_000_000);
        $f = rtrim(str_pad((string) ($scaled % 1_000_000), 6, '0', STR_PAD_LEFT), '0');

        return $f === '' ? (string) $w : $w.'.'.$f;
    }

    private function basisPoints(int $bp): string
    {
        return intdiv($bp, 100).','.str_pad((string) ($bp % 100), 2, '0', STR_PAD_LEFT).'%';
    }
}
