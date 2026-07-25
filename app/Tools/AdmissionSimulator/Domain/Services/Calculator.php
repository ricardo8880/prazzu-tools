<?php

declare(strict_types=1);

namespace App\Tools\AdmissionSimulator\Domain\Services;

use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\AdmissionSimulator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public const RULE_VERSION = '1.1.0';

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }

        $burden = $input->salary->percentage($input->monthlyBurden);
        $recurring = $input->salary->add($input->benefits)->add($burden);
        $oneOff = $input->exam->add($input->recruitment)->add($input->equipment)->add($input->training);
        $firstMonth = $recurring->add($oneOff);
        $firstYear = $recurring->multiply(12)->add($oneOff);

        $memory = new CalculationMemory(
            schemaVersion: self::RULE_VERSION,
            steps: [
                new CalculationMemoryStep('burden', 'Encargos e provisões mensais', 'salário × percentual informado', ['salary' => $input->salary->minorAmount(), 'monthly_burden' => $input->monthlyBurden->toDecimalString()], $burden->minorAmount(), 'Money::percentage, em centavos.'),
                new CalculationMemoryStep('recurring', 'Custo mensal recorrente', 'salário + benefícios + encargos/provisões', ['salary' => $input->salary->minorAmount(), 'benefits' => $input->benefits->minorAmount(), 'burden' => $burden->minorAmount()], $recurring->minorAmount(), 'Valores monetários em centavos.'),
                new CalculationMemoryStep('one_off', 'Custos únicos', 'exame + recrutamento + equipamento + treinamento', ['exam' => $input->exam->minorAmount(), 'recruitment' => $input->recruitment->minorAmount(), 'equipment' => $input->equipment->minorAmount(), 'training' => $input->training->minorAmount()], $oneOff->minorAmount(), 'Valores monetários em centavos.'),
                new CalculationMemoryStep('first_year', 'Projeção do primeiro ano', '(custo recorrente × 12) + custos únicos', ['recurring' => $recurring->minorAmount(), 'one_off' => $oneOff->minorAmount()], $firstYear->minorAmount(), 'Valores monetários em centavos.'),
            ],
            assumptions: ['O percentual mensal de encargos e provisões é declarado pelo usuário e deve refletir o enquadramento real do empregador.'],
            isEstimate: true,
        );

        return new ToolCalculationResult(
            'simulador-admissao',
            self::RULE_VERSION,
            [
                new ToolCalculationSummaryItem('first_month', 'Custo do primeiro mês', $firstMonth->formatPtBr()),
                new ToolCalculationSummaryItem('recurring', 'Custo mensal recorrente', $recurring->formatPtBr()),
                new ToolCalculationSummaryItem('one_off', 'Custos únicos de admissão', $oneOff->formatPtBr()),
                new ToolCalculationSummaryItem('annual', 'Projeção do primeiro ano', $firstYear->formatPtBr()),
            ],
            [
                'input' => $input->toArray(),
                'checklist' => ['Documento de identificação e CPF', 'CTPS Digital e dados do eSocial', 'Comprovante de endereço', 'Dados bancários', 'ASO admissional', 'Contrato de trabalho', 'Ficha de registro', 'Declarações de dependentes e benefícios', 'Opção de vale-transporte, quando aplicável', 'Ciência de políticas e segurança do trabalho'],
            ],
            calculationMemory: $memory,
        );
    }
}
