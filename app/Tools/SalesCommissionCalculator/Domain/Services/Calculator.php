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
            'sales-commission-calculator',
            '1.2.0',
            [
                new ToolCalculationSummaryItem('total_commission', 'Total commission', $total->formatEnUs()),
                new ToolCalculationSummaryItem('commissionable_sales', 'Commissionable sales', $commissionableSales->formatEnUs(), 'Gross sales minus entered refunds/chargebacks.'),
                new ToolCalculationSummaryItem('base_commission', 'Base commission rate', $base->formatEnUs()),
                new ToolCalculationSummaryItem('goal_bonus', 'Target bonus', $bonus->formatEnUs(), $goalReached ? 'Target reached.' : 'Target not reached or not defined.'),
                new ToolCalculationSummaryItem('goal_achievement', 'Target achievement', $achievement === null ? 'Target not defined' : number_format($achievement / 100, 2, ',', '.').' %'),
            ],
            [
                'input' => $input->toArray(),
                'commission_basis' => 'Gross sales minus entered refunds/chargebacks.',
            ],
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.2.0',
                steps: [
                    new CalculationMemoryStep(
                        key: 'commissionable_sales',
                        label: 'Commissionable sales',
                        formula: 'faturamento bruto - estornos',
                        inputs: ['sales_minor' => $input->sales->minorAmount(), 'reversals_minor' => $reversals->minorAmount()],
                        result: $commissionableSales->minorAmount(),
                    ),
                    new CalculationMemoryStep(
                        key: 'base_commission',
                        label: 'Base commission rate',
                        formula: 'commissionable sales × commission rate',
                        inputs: ['commissionable_sales_minor' => $commissionableSales->minorAmount(), 'rate' => $input->rate->toDecimalString()],
                        result: $base->minorAmount(),
                        roundingPolicy: 'Money::percentage com arredondamento monetário do Core.',
                    ),
                    new CalculationMemoryStep(
                        key: 'goal_bonus',
                        label: 'Target bonus',
                        formula: 'commissionable sales × bonus rate when the net target is reached',
                        inputs: ['commissionable_sales_minor' => $commissionableSales->minorAmount(), 'goal_minor' => $input->goal->minorAmount(), 'goal_bonus_rate' => $input->goalBonusRate->toDecimalString(), 'goal_reached' => $goalReached],
                        result: $bonus->minorAmount(),
                        roundingPolicy: 'Money::percentage com arredondamento monetário do Core.',
                    ),
                    new CalculationMemoryStep(
                        key: 'total_commission',
                        label: 'Total commission',
                        formula: 'base commission + target bonus',
                        inputs: ['base_commission_minor' => $base->minorAmount(), 'goal_bonus_minor' => $bonus->minorAmount()],
                        result: $total->minorAmount(),
                    ),
                ],
                assumptions: [
                    'Commissionable sales equal gross sales minus entered refunds/chargebacks.',
                    'Target attainment is evaluated on sales net of entered refunds/chargebacks.',
                    'The estimate does not apply compensation-plan rules for timing, payment, future chargebacks, or caps unless entered.',
                ],
                isEstimate: true,
            ),
        );
    }
}
