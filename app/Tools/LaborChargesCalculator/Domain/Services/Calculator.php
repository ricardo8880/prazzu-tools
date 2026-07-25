<?php

declare(strict_types=1);

namespace App\Tools\LaborChargesCalculator\Domain\Services;

use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\LaborChargesCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }
        if (! in_array($input->regime, ['general', 'simples_annex_iv', 'simples_other'], true)) {
            throw new InvalidArgumentException('Regime inválido.');
        }
        $thirteenth = $input->salary->divide(12);
        $vacation = $input->salary->divide(12);
        $vacationThird = $input->salary->divide(36);
        $incidenceBase = $input->salary->add($thirteenth)->add($vacation)->add($vacationThird);
        $fgts = $incidenceBase->percentage(Percentage::fromString('8'));
        $cppRate = $input->regime === 'simples_other' ? Percentage::zero() : Percentage::fromString('20');
        $ratRate = $input->regime === 'simples_other' ? Percentage::zero() : $input->rat;
        $thirdRate = str_starts_with($input->regime, 'simples_') ? Percentage::zero() : $input->thirdParties;
        $cpp = $incidenceBase->percentage($cppRate);
        $rat = $incidenceBase->percentage($ratRate);
        $third = $incidenceBase->percentage($thirdRate);
        $provisions = $thirteenth->add($vacation)->add($vacationThird);
        $charges = $fgts->add($cpp)->add($rat)->add($third);
        $total = $input->salary->add($input->benefits)->add($provisions)->add($charges);

        return new ToolCalculationResult('encargos-trabalhistas', '1.0.0', [
            new ToolCalculationSummaryItem('total_cost', 'Custo mensal total provisionado', $total->formatPtBr()),
            new ToolCalculationSummaryItem('provisions', 'Provisões de 13º e férias', $provisions->formatPtBr()),
            new ToolCalculationSummaryItem('fgts', 'FGTS provisionado', $fgts->formatPtBr(), '8% sobre a base com provisões.'),
            new ToolCalculationSummaryItem('patronal', 'CPP, RAT e terceiros', $cpp->add($rat)->add($third)->formatPtBr()),
        ], ['input' => $input->toArray(), 'memory' => [
            '13º mensal (1/12)' => $thirteenth->formatPtBr(), 'Férias mensais (1/12)' => $vacation->formatPtBr(),
            'Terço de férias mensal (1/36)' => $vacationThird->formatPtBr(), 'Base de incidência provisionada' => $incidenceBase->formatPtBr(),
            'CPP '.$cppRate->toDecimalString().'%' => $cpp->formatPtBr(), 'RAT '.$ratRate->toDecimalString().'%' => $rat->formatPtBr(),
            'Terceiros '.$thirdRate->toDecimalString().'%' => $third->formatPtBr(), 'Custo total' => $total->formatPtBr(),
        ]]);
    }
}
