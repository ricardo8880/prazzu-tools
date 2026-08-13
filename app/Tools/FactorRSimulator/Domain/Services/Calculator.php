<?php

declare(strict_types=1);

namespace App\Tools\FactorRSimulator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Money\Money;
use App\Core\Tax\Normative\FactorRRule;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\FactorRSimulator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function __construct(private readonly FactorRRule $rule = new FactorRRule) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }

        $payroll = $input->payroll12->minorAmount();
        $revenue = $input->revenue12->minorAmount();
        $threshold = $this->rule->threshold();
        $thresholdPayroll = $input->revenue12->percentage($threshold);

        if ($revenue === 0) {
            $factorBasisPoints = $payroll > 0 ? 2800 : 100;
        } else {
            if ($payroll > intdiv(PHP_INT_MAX, 10000)) {
                throw new InvalidArgumentException('Valor da folha fora do intervalo suportado.');
            }
            $factorBasisPoints = intdiv($payroll * 10000, $revenue);
        }

        $annexIII = $factorBasisPoints >= 2800;
        $gap = Money::fromMinor(max(0, $thresholdPayroll->minorAmount() - $payroll));
        $factor = $this->formatBasisPoints($factorBasisPoints);

        return new ToolCalculationResult(
            'simulador-fator-r',
            '1.1.0',
            [
                new ToolCalculationSummaryItem('factor_r', 'Fator R', $factor),
                new ToolCalculationSummaryItem('annex', 'Anexo aplicável pelo Fator R', $annexIII ? 'Anexo III' : 'Anexo V', $annexIII ? 'Fator R igual ou superior a 28%.' : 'Fator R inferior a 28%.'),
                new ToolCalculationSummaryItem('threshold_payroll', 'Folha necessária para 28%', $thresholdPayroll->formatPtBr()),
                new ToolCalculationSummaryItem('payroll_gap', 'Diferença até 28%', $gap->formatPtBr()),
            ],
            [
                'input' => $input->toArray(),
                'rule_version' => FactorRRule::VERSION,
            ],
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.0.0',
                steps: [
                    new CalculationMemoryStep('factor_r', 'Fator R', 'FS12 ÷ RBT12', ['fs12' => $payroll, 'rbt12' => $revenue], $factor),
                    new CalculationMemoryStep('threshold_payroll', 'Folha necessária para 28%', 'RBT12 × 28%', ['rbt12' => $revenue, 'threshold_basis_points' => 2800], $thresholdPayroll->minorAmount(), 'Arredondamento monetário em centavos.'),
                    new CalculationMemoryStep('payroll_gap', 'Diferença até o limite', 'máximo(0, folha de referência − FS12)', ['threshold_payroll' => $thresholdPayroll->minorAmount(), 'fs12' => $payroll], $gap->minorAmount()),
                ],
                normativeRules: [$this->rule->snapshot(ReferenceDate::fromString('2026-07-25'))],
                assumptions: ['FS12 e RBT12 devem corresponder aos doze meses anteriores à competência analisada.', 'A classificação considera exclusivamente o critério do Fator R; atividade e demais requisitos permanecem sob responsabilidade do utilizador.'],
            ),
        );
    }

    private function formatBasisPoints(int $basisPoints): string
    {
        $whole = intdiv($basisPoints, 100);
        $fraction = str_pad((string) ($basisPoints % 100), 2, '0', STR_PAD_LEFT);

        return number_format($whole, 0, ',', '.').','.$fraction.' %';
    }
}
