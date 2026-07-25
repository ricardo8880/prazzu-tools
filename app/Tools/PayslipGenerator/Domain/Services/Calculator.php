<?php

declare(strict_types=1);

namespace App\Tools\PayslipGenerator\Domain\Services;

use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\PayslipGenerator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public const RULE_VERSION = '1.1.0';

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }

        $earnings = $input->salary->add($input->otherEarnings);
        $deductions = $input->inss->add($input->irrf)->add($input->otherDeductions);

        if ($deductions->minorAmount() > $earnings->minorAmount()) {
            throw new InvalidArgumentException('Descontos superiores aos proventos.');
        }

        $net = $earnings->subtract($deductions);
        $memory = new CalculationMemory(
            schemaVersion: self::RULE_VERSION,
            steps: [
                new CalculationMemoryStep('earnings', 'Total de proventos', 'salário + outros proventos', ['salary' => $input->salary->minorAmount(), 'other_earnings' => $input->otherEarnings->minorAmount()], $earnings->minorAmount(), 'Valores monetários em centavos.'),
                new CalculationMemoryStep('deductions', 'Total de descontos', 'INSS + IRRF + outros descontos', ['inss' => $input->inss->minorAmount(), 'irrf' => $input->irrf->minorAmount(), 'other_deductions' => $input->otherDeductions->minorAmount()], $deductions->minorAmount(), 'Valores monetários em centavos.'),
                new CalculationMemoryStep('net', 'Líquido a receber', 'proventos - descontos', ['earnings' => $earnings->minorAmount(), 'deductions' => $deductions->minorAmount()], $net->minorAmount(), 'Valores monetários em centavos.'),
            ],
            assumptions: ['INSS, IRRF e demais descontos são informados pelo usuário; esta ferramenta emite o demonstrativo e não recalcula folhas completas.'],
        );

        return new ToolCalculationResult(
            'gerador-holerite',
            self::RULE_VERSION,
            [
                new ToolCalculationSummaryItem('earnings', 'Total de proventos', $earnings->formatPtBr()),
                new ToolCalculationSummaryItem('deductions', 'Total de descontos', $deductions->formatPtBr()),
                new ToolCalculationSummaryItem('net', 'Líquido a receber', $net->formatPtBr()),
            ],
            [
                'input' => $input->toArray(),
                'document' => [
                    'employee' => $input->name,
                    'document' => $input->document,
                    'employer' => $input->employer,
                    'competence' => $input->competence,
                    'items' => [
                        'Salário' => $input->salary->formatPtBr(),
                        'Outros proventos' => $input->otherEarnings->formatPtBr(),
                        'INSS' => '-'.$input->inss->formatPtBr(),
                        'IRRF' => '-'.$input->irrf->formatPtBr(),
                        'Outros descontos' => '-'.$input->otherDeductions->formatPtBr(),
                    ],
                    'earnings' => $earnings->formatPtBr(),
                    'deductions' => $deductions->formatPtBr(),
                    'net' => $net->formatPtBr(),
                ],
            ],
            calculationMemory: $memory,
        );
    }
}
