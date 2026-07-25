<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\SalesCommissionCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }

        $base = $input->sales->percentage($input->rate);
        $goalReached = $input->goal->minorAmount() > 0 && $input->sales->minorAmount() >= $input->goal->minorAmount();
        $bonus = $goalReached ? $input->sales->percentage($input->goalBonusRate) : Money::zero();
        $total = $base->add($bonus);
        $achievement = $input->goal->minorAmount() > 0
            ? intdiv($input->sales->minorAmount() * 10000, $input->goal->minorAmount())
            : null;

        return new ToolCalculationResult(
            'comissao-vendedores',
            '1.0.0',
            [
                new ToolCalculationSummaryItem('total_commission', 'Comissão total', $total->formatPtBr()),
                new ToolCalculationSummaryItem('base_commission', 'Comissão-base', $base->formatPtBr()),
                new ToolCalculationSummaryItem('goal_bonus', 'Bônus por meta', $bonus->formatPtBr(), $goalReached ? 'Meta atingida.' : 'Meta não atingida ou não definida.'),
                new ToolCalculationSummaryItem('goal_achievement', 'Atingimento da meta', $achievement === null ? 'Meta não definida' : number_format($achievement / 100, 2, ',', '.').' %'),
            ],
            [
                'input' => $input->toArray(),
                'memory' => [
                    'Comissão-base = faturamento × percentual' => $base->formatPtBr(),
                    'Bônus = faturamento × bônus da meta (se atingida)' => $bonus->formatPtBr(),
                    'Total = comissão-base + bônus' => $total->formatPtBr(),
                ],
            ],
        );
    }
}
