<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\AdmissionSimulator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $i): ToolCalculationResult
    {
        if (! $i instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }$burden = $i->salary->percentage($i->monthlyBurden);
        $recurring = $i->salary->add($i->benefits)->add($burden);
        $oneOff = $i->exam->add($i->recruitment)->add($i->equipment)->add($i->training);
        $first = $recurring->add($oneOff);

        return new ToolCalculationResult('simulador-admissao', '1.0.0', [new ToolCalculationSummaryItem('first_month', 'Custo do primeiro mês', $first->formatPtBr()), new ToolCalculationSummaryItem('recurring', 'Custo mensal recorrente', $recurring->formatPtBr()), new ToolCalculationSummaryItem('one_off', 'Custos únicos de admissão', $oneOff->formatPtBr()), new ToolCalculationSummaryItem('annual', 'Projeção do primeiro ano', $recurring->multiply(12)->add($oneOff)->formatPtBr())], ['input' => $i->toArray(), 'memory' => ['Salário' => $i->salary->formatPtBr(), 'Benefícios' => $i->benefits->formatPtBr(), 'Encargos/provisões' => $burden->formatPtBr(), 'Custos únicos' => $oneOff->formatPtBr()], 'checklist' => ['Documento de identificação e CPF', 'CTPS Digital e dados do eSocial', 'Comprovante de endereço', 'Dados bancários', 'ASO admissional', 'Contrato de trabalho', 'Ficha de registro', 'Declarações de dependentes e benefícios', 'Opção de vale-transporte, quando aplicável', 'Ciência de políticas e segurança do trabalho']]);
    }
}
