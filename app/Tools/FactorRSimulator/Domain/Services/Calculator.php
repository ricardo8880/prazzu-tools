<?php

declare(strict_types=1);

namespace App\Tools\FactorRSimulator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\FactorRSimulator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public const RULE_VERSION = 'CGSN-140-2018-art-26-rev-2026-04';

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }

        $payroll = $input->payroll12->minorAmount();
        $revenue = $input->revenue12->minorAmount();
        $thresholdPayroll = $input->revenue12->percentage(Percentage::fromString('28'));

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
            '1.0.0',
            [
                new ToolCalculationSummaryItem('factor_r', 'Fator R', $factor),
                new ToolCalculationSummaryItem('annex', 'Anexo aplicável pelo Fator R', $annexIII ? 'Anexo III' : 'Anexo V', $annexIII ? 'Fator R igual ou superior a 28%.' : 'Fator R inferior a 28%.'),
                new ToolCalculationSummaryItem('threshold_payroll', 'Folha necessária para 28%', $thresholdPayroll->formatPtBr()),
                new ToolCalculationSummaryItem('payroll_gap', 'Diferença até 28%', $gap->formatPtBr()),
            ],
            [
                'input' => $input->toArray(),
                'rule_version' => self::RULE_VERSION,
                'memory' => [
                    'FS12 informada' => $input->payroll12->formatPtBr(),
                    'RBT12 informada' => $input->revenue12->formatPtBr(),
                    'Fator R = FS12 ÷ RBT12' => $factor,
                    'Limite para o Anexo III' => '28,00 %',
                    'Folha de referência = RBT12 × 28%' => $thresholdPayroll->formatPtBr(),
                ],
            ],
        );
    }

    private function formatBasisPoints(int $basisPoints): string
    {
        $whole = intdiv($basisPoints, 100);
        $fraction = str_pad((string) ($basisPoints % 100), 2, '0', STR_PAD_LEFT);

        return number_format($whole, 0, ',', '.').','.$fraction.' %';
    }
}
