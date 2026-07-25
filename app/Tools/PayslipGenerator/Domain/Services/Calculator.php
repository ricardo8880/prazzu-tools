<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\PayslipGenerator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $i): ToolCalculationResult
    {
        if (! $i instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }$earn = $i->salary->add($i->otherEarnings);
        $ded = $i->inss->add($i->irrf)->add($i->otherDeductions);
        if ($ded->minorAmount() > $earn->minorAmount()) {
            throw new InvalidArgumentException('Descontos superiores aos proventos.');
        }$net = $earn->subtract($ded);

        return new ToolCalculationResult('gerador-holerite', '1.0.0', [new ToolCalculationSummaryItem('earnings', 'Total de proventos', $earn->formatPtBr()), new ToolCalculationSummaryItem('deductions', 'Total de descontos', $ded->formatPtBr()), new ToolCalculationSummaryItem('net', 'Líquido a receber', $net->formatPtBr())], ['input' => $i->toArray(), 'document' => ['employee' => $i->name, 'document' => $i->document, 'employer' => $i->employer, 'competence' => $i->competence, 'items' => ['Salário' => $i->salary->formatPtBr(), 'Outros proventos' => $i->otherEarnings->formatPtBr(), 'INSS' => '-'.$i->inss->formatPtBr(), 'IRRF' => '-'.$i->irrf->formatPtBr(), 'Outros descontos' => '-'.$i->otherDeductions->formatPtBr()], 'earnings' => $earn->formatPtBr(), 'deductions' => $ded->formatPtBr(), 'net' => $net->formatPtBr()]]);
    }
}
