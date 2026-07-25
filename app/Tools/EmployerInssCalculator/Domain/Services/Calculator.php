<?php

declare(strict_types=1);

namespace App\Tools\EmployerInssCalculator\Domain\Services;

use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\EmployerInssCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public const RULE_VERSION = 'Lei-8212-art-22-rev-2026-07';

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }
        if (! in_array($input->regime, ['general', 'simples_annex_iv', 'simples_other'], true)) {
            throw new InvalidArgumentException('Regime não suportado.');
        }

        $cppRate = $input->regime === 'simples_other' ? Percentage::zero() : Percentage::fromString('20');
        $ratRate = $input->regime === 'simples_other' ? Percentage::zero() : $input->adjustedRat;
        $thirdRate = str_starts_with($input->regime, 'simples_') ? Percentage::zero() : $input->thirdParties;
        $cpp = $input->payroll->percentage($cppRate);
        $rat = $input->payroll->percentage($ratRate);
        $third = $input->payroll->percentage($thirdRate);
        $total = $cpp->add($rat)->add($third);
        $effective = $cppRate->millionthsOfPercent() + $ratRate->millionthsOfPercent() + $thirdRate->millionthsOfPercent();

        return new ToolCalculationResult('inss-patronal', '1.0.0', [
            new ToolCalculationSummaryItem('total', 'Total patronal estimado', $total->formatPtBr()),
            new ToolCalculationSummaryItem('cpp', 'CPP', $cpp->formatPtBr(), $cppRate->toDecimalString().'%'),
            new ToolCalculationSummaryItem('rat', 'RAT ajustado', $rat->formatPtBr(), $ratRate->toDecimalString().'%'),
            new ToolCalculationSummaryItem('third_parties', 'Terceiros', $third->formatPtBr(), $thirdRate->toDecimalString().'%'),
        ], [
            'input' => $input->toArray(),
            'rule_version' => self::RULE_VERSION,
            'effective_rate_millionths' => $effective,
            'memory' => [
                'CPP = folha × '.$cppRate->toDecimalString().'%' => $cpp->formatPtBr(),
                'RAT ajustado = folha × '.$ratRate->toDecimalString().'%' => $rat->formatPtBr(),
                'Terceiros = folha × '.$thirdRate->toDecimalString().'%' => $third->formatPtBr(),
                'Total = CPP + RAT + terceiros' => $total->formatPtBr(),
            ],
        ]);
    }
}
