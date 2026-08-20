<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
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

        $reversals = $input->reversals ?? Money::fromMinor(0, $input->sales->currency());

        if ($reversals->minorAmount() < 0 || $reversals->minorAmount() > $input->sales->minorAmount()) {
            throw new InvalidArgumentException('Os estornos devem estar entre zero e o faturamento bruto.');
        }

        $commissionableSales = $input->sales->subtract($reversals);
        $base = $commissionableSales->percentage($input->rate);
        $goalReached = $input->goal->minorAmount() > 0 && $commissionableSales->minorAmount() >= $input->goal->minorAmount();
        $bonus = $goalReached ? $commissionableSales->percentage($input->goalBonusRate) : Money::fromMinor(0, $input->sales->currency());
        $total = $base->add($bonus);
        $achievement = $input->goal->minorAmount() > 0
            ? intdiv($commissionableSales->minorAmount() * 10000, $input->goal->minorAmount())
            : null;

        return new ToolCalculationResult(
            'comissao-vendedores',
            '1.2.0',
            [
                new ToolCalculationSummaryItem('total_commission', 'Comissão total', $total->formatPtBr()),
                new ToolCalculationSummaryItem('commissionable_sales', 'Base comissionável', $commissionableSales->formatPtBr(), 'Faturamento bruto menos estornos informados.'),
                new ToolCalculationSummaryItem('base_commission', 'Comissão-base', $base->formatPtBr()),
                new ToolCalculationSummaryItem('goal_bonus', 'Bônus por meta', $bonus->formatPtBr(), $goalReached ? 'Meta atingida.' : 'Meta não atingida ou não definida.'),
                new ToolCalculationSummaryItem('goal_achievement', 'Atingimento da meta', $achievement === null ? 'Meta não definida' : number_format($achievement / 100, 2, ',', '.').' %'),
            ],
            [
                'input' => $input->toArray(),
                'commission_basis' => 'Faturamento bruto menos estornos informados.',
            ],
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.2.0',
                steps: [
                    new CalculationMemoryStep(
                        key: 'commissionable_sales',
                        label: 'Base comissionável',
                        formula: 'faturamento bruto - estornos',
                        inputs: ['sales_minor' => $input->sales->minorAmount(), 'reversals_minor' => $reversals->minorAmount()],
                        result: $commissionableSales->minorAmount(),
                    ),
                    new CalculationMemoryStep(
                        key: 'base_commission',
                        label: 'Comissão-base',
                        formula: 'base comissionável × percentual de comissão',
                        inputs: ['commissionable_sales_minor' => $commissionableSales->minorAmount(), 'rate' => $input->rate->toDecimalString()],
                        result: $base->minorAmount(),
                        roundingPolicy: 'Money::percentage com arredondamento monetário do Core.',
                    ),
                    new CalculationMemoryStep(
                        key: 'goal_bonus',
                        label: 'Bônus por meta',
                        formula: 'base comissionável × percentual de bônus, somente quando a meta líquida é atingida',
                        inputs: ['commissionable_sales_minor' => $commissionableSales->minorAmount(), 'goal_minor' => $input->goal->minorAmount(), 'goal_bonus_rate' => $input->goalBonusRate->toDecimalString(), 'goal_reached' => $goalReached],
                        result: $bonus->minorAmount(),
                        roundingPolicy: 'Money::percentage com arredondamento monetário do Core.',
                    ),
                    new CalculationMemoryStep(
                        key: 'total_commission',
                        label: 'Comissão total',
                        formula: 'comissão-base + bônus por meta',
                        inputs: ['base_commission_minor' => $base->minorAmount(), 'goal_bonus_minor' => $bonus->minorAmount()],
                        result: $total->minorAmount(),
                    ),
                ],
                assumptions: [
                    'A base de comissão é o faturamento bruto menos os estornos informados.',
                    'A meta também é avaliada sobre a base líquida de estornos.',
                    'O cálculo não aplica regras contratuais de competência, pagamento, devolução futura ou teto de comissão não informadas.',
                ],
                isEstimate: true,
            ),
        );
    }
}
