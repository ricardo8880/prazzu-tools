<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\WorkIncomeStatementGenerator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $i): ToolCalculationResult
    {
        if (! $i instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }$text = "Declaramos, para os devidos fins, que {$i->name}, documento {$i->document}, exerce a função de {$i->occupation} junto a {$i->employer} desde {$i->startDate}, percebendo renda mensal de {$i->monthlyIncome->formatPtBr()}.";

        return new ToolCalculationResult('declaracao-trabalho-renda', '1.0.0', [new ToolCalculationSummaryItem('monthly_income', 'Renda mensal declarada', $i->monthlyIncome->formatPtBr()), new ToolCalculationSummaryItem('worker', 'Trabalhador', $i->name), new ToolCalculationSummaryItem('occupation', 'Função', $i->occupation)], ['input' => $i->toArray(), 'document' => ['title' => 'Declaração de Trabalho e Renda', 'text' => $text, 'location' => "{$i->city}, {$i->issueDate}", 'signer' => $i->employer]]);
    }
}
