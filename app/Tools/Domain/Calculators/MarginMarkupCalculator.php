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
    public const RULE_VERSION = '1.0.0';
    private const PERCENT_FACTOR = 100_000_000;

    public function calculate(Money $baseCost, Money $additionalCosts, Percentage $desiredMargin): MarginMarkupResult
    {
        $marginUnits = $desiredMargin->millionthsOfPercent();

        if ($baseCost->minorAmount() < 0 || $additionalCosts->minorAmount() < 0) {
            throw new InvalidValue('Os custos não podem ser negativos.');
        }

        if ($marginUnits < 0 || $marginUnits >= self::PERCENT_FACTOR) {
            throw new InvalidValue('A margem desejada deve ser maior ou igual a zero e menor que 100%.');
        }

        $totalCost = $baseCost->add($additionalCosts);

        if ($totalCost->minorAmount() === 0) {
            throw new InvalidValue('O custo total deve ser maior que zero.');
        }

        $saleMinor = IntegerRounding::divide(
            $totalCost->minorAmount() * self::PERCENT_FACTOR,
            self::PERCENT_FACTOR - $marginUnits,
            RoundingMode::HalfUp,
        );
        $salePrice = Money::fromMinor($saleMinor, $totalCost->currency());
        $profit = $salePrice->subtract($totalCost);
        $markupUnits = IntegerRounding::divide(
            $profit->minorAmount() * self::PERCENT_FACTOR,
            $totalCost->minorAmount(),
            RoundingMode::HalfUp,
        );

        return new MarginMarkupResult(
            totalCost: $totalCost,
            salePrice: $salePrice,
            profit: $profit,
            margin: $desiredMargin,
            markup: Percentage::fromString($this->formatUnits($markupUnits)),
            ruleVersion: self::RULE_VERSION,
        );
    }

    private function formatUnits(int $units): string
    {
        $whole = intdiv($units, 1_000_000);
        $fraction = str_pad((string) ($units % 1_000_000), 6, '0', STR_PAD_LEFT);

        return $whole.'.'.$fraction;
    }
}
