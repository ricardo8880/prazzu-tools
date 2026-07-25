<?php

declare(strict_types=1);

namespace App\Tools\LateDasCalculator\Domain\Services;

use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\LateDasCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public const RULE_VERSION = 'LC-123-art-35-2026';

    public function calculate(ToolCalculationInput $i): ToolCalculationResult
    {
        if (! $i instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }
        $late = $i->paymentDate > $i->dueDate;
        $days = $late ? (int) $i->dueDate->diff($i->paymentDate)->format('%a') : 0;
        $fineBp = min(2000, $days * 33);
        $fineRate = Percentage::fromBasisPoints($fineBp);
        $interestRate = $late ? Percentage::fromString($this->scaledPercent(1_000_000 + $i->accumulatedSelic->millionthsOfPercent())) : Percentage::zero();
        $fine = $i->principal->percentage($fineRate);
        $interest = $i->principal->percentage($interestRate);
        $total = $i->principal->add($fine)->add($interest);

        return new ToolCalculationResult('das-em-atraso', '1.0.0', [
            new ToolCalculationSummaryItem('total', 'DAS atualizado', $total->formatPtBr()), new ToolCalculationSummaryItem('fine', 'Multa de mora', $fine->formatPtBr(), $this->basisPoints($fineBp)), new ToolCalculationSummaryItem('interest', 'Juros de mora', $interest->formatPtBr(), $interestRate->toDecimalString().'%'), new ToolCalculationSummaryItem('days_late', 'Dias em atraso', (string) $days),
        ], ['input' => $i->toArray(), 'rule_version' => self::RULE_VERSION, 'memory' => ['Principal' => $i->principal->formatPtBr(), 'Multa = 0,33% ao dia, limitada a 20%' => $fine->formatPtBr(), 'Selic acumulada informada' => $i->accumulatedSelic->toDecimalString().'%', 'Juros = Selic acumulada + 1% do mês do pagamento' => $interest->formatPtBr(), 'Total' => $total->formatPtBr()]]);
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
