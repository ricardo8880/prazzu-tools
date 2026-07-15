<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Application\Actions;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Core\Money\Percentage;

final readonly class SimulatePricingScenarios
{
    public function __construct(private CalculateMarginMarkup $calculator) {}

    /**
     * @param array<string, mixed> $input
     * @return array<int, array<string, string>>
     */
    public function execute(array $input): array
    {
        $results = [];

        foreach ($input['scenarios'] as $scenario) {
            $adjustment = Percentage::fromString($this->valueOrZero($scenario['cost_adjustment'] ?? null));
            $baseCost = $this->adjustMoney($input['base_cost'], $adjustment);
            $additionalCosts = $this->adjustMoney($this->valueOrZero($input['additional_costs'] ?? null), $adjustment);
            $freightCost = $this->adjustMoney($this->valueOrZero($input['freight_cost'] ?? null), $adjustment);
            $packagingCost = $this->adjustMoney($this->valueOrZero($input['packaging_cost'] ?? null), $adjustment);
            $fixedExpenses = $this->adjustMoney($this->valueOrZero($input['fixed_expenses'] ?? null), $adjustment);

            $calculation = $this->calculator->execute([
                'base_cost' => $baseCost,
                'additional_costs' => $additionalCosts,
                'freight_cost' => $freightCost,
                'packaging_cost' => $packagingCost,
                'fixed_expenses' => $fixedExpenses,
                'desired_margin' => (string) $scenario['desired_margin'],
                'taxes_percentage' => $this->valueOrZero($scenario['taxes_percentage'] ?? null),
                'commission_percentage' => $this->valueOrZero($scenario['commission_percentage'] ?? null),
                'card_fees_percentage' => $this->valueOrZero($scenario['card_fees_percentage'] ?? null),
                'marketplace_fees_percentage' => $this->valueOrZero($scenario['marketplace_fees_percentage'] ?? null),
            ]);

            $discount = Percentage::fromString($this->valueOrZero($scenario['discount_percentage'] ?? null));
            $finalPrice = $calculation->salePrice->subtract($calculation->salePrice->percentage($discount));
            $variableRate = $this->sumPercentages($scenario);
            $variableDeductions = $finalPrice->percentage($variableRate);
            $netProfit = $finalPrice->subtract($calculation->totalCost)->subtract($variableDeductions);
            $effectiveMargin = $finalPrice->minorAmount() > 0
                ? $this->ratioAsPercentage($netProfit->minorAmount(), $finalPrice->minorAmount())
                : '0%';

            $results[] = [
                'name' => trim((string) $scenario['name']),
                'cost_adjustment' => $adjustment->toDecimalString().'%',
                'desired_margin' => Percentage::fromString((string) $scenario['desired_margin'])->toDecimalString().'%',
                'discount' => $discount->toDecimalString().'%',
                'total_cost' => $calculation->totalCost->formatPtBr(),
                'list_price' => $calculation->salePrice->formatPtBr(),
                'final_price' => $finalPrice->formatPtBr(),
                'net_profit' => $netProfit->formatPtBr(),
                'effective_margin' => $effectiveMargin,
                'markup_multiplier' => $calculation->markupMultiplier,
            ];
        }

        return $results;
    }

    private function adjustMoney(string $value, Percentage $adjustment): string
    {
        $money = Money::fromDecimal($value);
        $adjusted = $money->add($money->percentage($adjustment));

        if ($adjusted->minorAmount() < 0) {
            throw new InvalidValue('O ajuste de custo não pode gerar valor negativo.');
        }

        return $this->decimalFromMinor($adjusted->minorAmount());
    }

    /** @param array<string, mixed> $scenario */
    private function sumPercentages(array $scenario): Percentage
    {
        $total = 0;
        foreach (['taxes_percentage', 'commission_percentage', 'card_fees_percentage', 'marketplace_fees_percentage'] as $field) {
            $total += Percentage::fromString($this->valueOrZero($scenario[$field] ?? null))->millionthsOfPercent();
        }

        return Percentage::fromString($this->percentageStringFromMillionths($total));
    }

    private function ratioAsPercentage(int $part, int $whole): string
    {
        $scaled = intdiv(($part * 100_000_000) + intdiv($whole, 2), $whole);
        return $this->percentageStringFromMillionths($scaled).'%';
    }

    private function decimalFromMinor(int $minor): string
    {
        $absolute = abs($minor);
        return ($minor < 0 ? '-' : '').intdiv($absolute, 100).','.str_pad((string) ($absolute % 100), 2, '0', STR_PAD_LEFT);
    }

    private function percentageStringFromMillionths(int $value): string
    {
        $negative = $value < 0;
        $absolute = abs($value);
        $whole = intdiv($absolute, 1_000_000);
        $fraction = rtrim(str_pad((string) ($absolute % 1_000_000), 6, '0', STR_PAD_LEFT), '0');
        return ($negative ? '-' : '').$whole.($fraction === '' ? '' : '.'.$fraction);
    }

    private function valueOrZero(mixed $value): string
    {
        return $value === null || trim((string) $value) === '' ? '0' : (string) $value;
    }
}
