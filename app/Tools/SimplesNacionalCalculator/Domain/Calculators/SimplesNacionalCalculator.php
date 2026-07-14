<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Calculators;

use App\Core\Exceptions\InvalidValue;
use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Data\SimplesNacionalResult;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Domain\Rules\SimplesNacionalTaxTable;

final readonly class SimplesNacionalCalculator
{
    private const PERCENT_SCALE = 100_000_000;

    public function __construct(private SimplesNacionalTaxTable $taxTable) {}

    public function calculate(TaxAnnex $annex, Money $rbt12, Money $monthlyRevenue): SimplesNacionalResult
    {
        if ($rbt12->minorAmount() <= 0) {
            throw new InvalidValue('O RBT12 deve ser maior que zero.');
        }

        if ($monthlyRevenue->minorAmount() < 0) {
            throw new InvalidValue('A receita do período não pode ser negativa.');
        }

        if ($rbt12->currency() !== $monthlyRevenue->currency()) {
            throw new InvalidValue('O RBT12 e a receita do período devem usar a mesma moeda.');
        }

        $bracket = $this->taxTable->bracketFor($annex, $rbt12);
        $effectiveRate = $this->effectiveRate($rbt12, $bracket->nominalRate, $bracket->deduction);

        return new SimplesNacionalResult(
            annex: $annex,
            rbt12: $rbt12,
            monthlyRevenue: $monthlyRevenue,
            bracket: $bracket,
            effectiveRate: $effectiveRate,
            estimatedDas: $monthlyRevenue->percentage($effectiveRate),
            ruleVersion: SimplesNacionalTaxTable::RULE_VERSION,
            ruleValidFrom: SimplesNacionalTaxTable::VALID_FROM,
        );
    }

    private function effectiveRate(Money $rbt12, Percentage $nominalRate, Money $deduction): Percentage
    {
        $numerator = ($rbt12->minorAmount() * $nominalRate->millionthsOfPercent())
            - ($deduction->minorAmount() * self::PERCENT_SCALE);

        $units = IntegerRounding::divide(
            $numerator,
            $rbt12->minorAmount(),
            RoundingMode::HalfUp,
        );

        if ($units < 0) {
            throw new InvalidValue('A alíquota efetiva calculada não pode ser negativa.');
        }

        return Percentage::fromString($this->formatPercentageUnits($units));
    }

    private function formatPercentageUnits(int $units): string
    {
        $whole = intdiv($units, 1_000_000);
        $fraction = str_pad((string) ($units % 1_000_000), 6, '0', STR_PAD_LEFT);

        return $whole.'.'.$fraction;
    }
}
