<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Domain\Calculators;

use App\Core\Exceptions\InvalidValue;
use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\MarginMarkupCalculator\Domain\Data\MarginMarkupResult;

final class MarginMarkupCalculator
{
    public const RULE_VERSION = '2.0.0';

    private const PERCENT_FACTOR = 100_000_000;

    public function calculate(
        Money $baseCost,
        Money $additionalCosts,
        Money $freightCost,
        Money $packagingCost,
        Money $fixedExpenses,
        Percentage $desiredMargin,
        Percentage $taxes,
        Percentage $commission,
        Percentage $cardFees,
        Percentage $marketplaceFees,
    ): MarginMarkupResult {
        $costs = [$baseCost, $additionalCosts, $freightCost, $packagingCost, $fixedExpenses];

        foreach ($costs as $cost) {
            if ($cost->minorAmount() < 0) {
                throw new InvalidValue('Os custos e as despesas não podem ser negativos.');
            }
        }

        $totalCost = array_reduce(
            $costs,
            static fn (Money $total, Money $cost): Money => $total->add($cost),
            Money::zero($baseCost->currency()),
        );

        if ($totalCost->minorAmount() === 0) {
            throw new InvalidValue('O custo total deve ser maior que zero.');
        }

        $percentageUnits = array_map(
            static fn (Percentage $percentage): int => $percentage->millionthsOfPercent(),
            [$desiredMargin, $taxes, $commission, $cardFees, $marketplaceFees],
        );

        foreach ($percentageUnits as $units) {
            if ($units < 0) {
                throw new InvalidValue('Margem, impostos, comissão e taxas não podem ser negativos.');
            }
        }

        $totalPercentageUnits = array_sum($percentageUnits);

        if ($totalPercentageUnits >= self::PERCENT_FACTOR) {
            throw new InvalidValue('A soma da margem, impostos, comissão e taxas deve ser menor que 100%.');
        }

        if ($totalCost->minorAmount() > intdiv(PHP_INT_MAX, self::PERCENT_FACTOR)) {
            throw new InvalidValue('O custo total informado é muito alto para o cálculo.');
        }

        $saleMinor = IntegerRounding::divide(
            $totalCost->minorAmount() * self::PERCENT_FACTOR,
            self::PERCENT_FACTOR - $totalPercentageUnits,
            RoundingMode::HalfUp,
        );
        $salePrice = Money::fromMinor($saleMinor, $totalCost->currency());

        $taxesAmount = $salePrice->percentage($taxes);
        $commissionAmount = $salePrice->percentage($commission);
        $cardFeesAmount = $salePrice->percentage($cardFees);
        $marketplaceFeesAmount = $salePrice->percentage($marketplaceFees);
        $grossProfit = $salePrice->subtract($totalCost);
        $netProfit = $grossProfit
            ->subtract($taxesAmount)
            ->subtract($commissionAmount)
            ->subtract($cardFeesAmount)
            ->subtract($marketplaceFeesAmount);

        $markupUnits = IntegerRounding::divide(
            $grossProfit->minorAmount() * self::PERCENT_FACTOR,
            $totalCost->minorAmount(),
            RoundingMode::HalfUp,
        );
        $multiplierScaled = IntegerRounding::divide(
            $salePrice->minorAmount() * 10_000,
            $totalCost->minorAmount(),
            RoundingMode::HalfUp,
        );

        return new MarginMarkupResult(
            totalCost: $totalCost,
            salePrice: $salePrice,
            grossProfit: $grossProfit,
            netProfit: $netProfit,
            taxesAmount: $taxesAmount,
            commissionAmount: $commissionAmount,
            cardFeesAmount: $cardFeesAmount,
            marketplaceFeesAmount: $marketplaceFeesAmount,
            margin: $desiredMargin,
            markup: Percentage::fromString($this->formatPercentageUnits($markupUnits)),
            markupMultiplier: $this->formatMultiplier($multiplierScaled),
            ruleVersion: self::RULE_VERSION,
        );
    }

    private function formatPercentageUnits(int $units): string
    {
        $whole = intdiv($units, 1_000_000);
        $fraction = str_pad((string) ($units % 1_000_000), 6, '0', STR_PAD_LEFT);

        return $whole.'.'.$fraction;
    }

    private function formatMultiplier(int $scaled): string
    {
        $whole = intdiv($scaled, 10_000);
        $fraction = rtrim(str_pad((string) ($scaled % 10_000), 4, '0', STR_PAD_LEFT), '0');

        return $fraction === '' ? (string) $whole : $whole.','.$fraction;
    }
}
