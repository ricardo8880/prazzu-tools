<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\IncomeStatementGenerator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $i): ToolCalculationResult
    {
        if (! $i instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }$ded = $i->inss->add($i->irrf)->add($i->otherDeductions);
        if ($ded->minorAmount() > $i->gross->minorAmount()) {
            throw new InvalidArgumentException('Deduções superiores aos rendimentos.');
        }$net = $i->gross->subtract($ded);

        return new ToolCalculationResult('declaracao-rendimentos', '1.0.0', [new ToolCalculationSummaryItem('gross', 'Rendimentos brutos', $i->gross->formatPtBr()), new ToolCalculationSummaryItem('deductions', 'Deduções informadas', $ded->formatPtBr()), new ToolCalculationSummaryItem('net', 'Rendimento líquido', $net->formatPtBr())], ['input' => $i->toArray(), 'document' => ['title' => 'Declaração de Rendimentos', 'payer' => $i->payer, 'beneficiary' => $i->name, 'document' => $i->document, 'year' => $i->year, 'gross' => $i->gross->formatPtBr(), 'inss' => $i->inss->formatPtBr(), 'irrf' => $i->irrf->formatPtBr(), 'other' => $i->otherDeductions->formatPtBr(), 'net' => $net->formatPtBr()]]);
    }
}
