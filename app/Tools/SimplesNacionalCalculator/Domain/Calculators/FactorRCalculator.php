<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Calculators;

use App\Core\Exceptions\InvalidValue;
use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Data\FactorRResult;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;

final readonly class FactorRCalculator
{
    public const THRESHOLD = '28';
    public const RULE_VERSION = 'lc-123-2006-factor-r-2018';
    public const VALID_FROM = '2018-01-01';

    private const PERCENT_SCALE = 100_000_000;
    private const THRESHOLD_UNITS = 28_000_000;

    public function calculate(Money $payroll12, Money $rbt12): FactorRResult
    {
        if ($payroll12->minorAmount() < 0) {
            throw new InvalidValue('A folha de salários dos últimos 12 meses não pode ser negativa.');
        }

        if ($rbt12->minorAmount() <= 0) {
            throw new InvalidValue('O RBT12 deve ser maior que zero para calcular o Fator R.');
        }

        if ($payroll12->currency() !== $rbt12->currency()) {
            throw new InvalidValue('A folha de salários e o RBT12 devem usar a mesma moeda.');
        }

        if ($payroll12->minorAmount() > intdiv(PHP_INT_MAX, self::PERCENT_SCALE)) {
            throw new InvalidValue('Os valores informados estão fora do intervalo suportado para o cálculo do Fator R.');
        }

        $factorUnits = IntegerRounding::divide(
            $payroll12->minorAmount() * self::PERCENT_SCALE,
            $rbt12->minorAmount(),
            RoundingMode::HalfUp,
        );

        $factorR = Percentage::fromString($this->formatPercentageUnits($factorUnits));

        return new FactorRResult(
            payroll12: $payroll12,
            rbt12: $rbt12,
            factorR: $factorR,
            threshold: Percentage::fromString(self::THRESHOLD),
            applicableAnnex: $factorUnits >= self::THRESHOLD_UNITS ? TaxAnnex::III : TaxAnnex::V,
            ruleVersion: self::RULE_VERSION,
            ruleValidFrom: self::VALID_FROM,
        );
    }

    private function formatPercentageUnits(int $units): string
    {
        $whole = intdiv($units, 1_000_000);
        $fraction = str_pad((string) ($units % 1_000_000), 6, '0', STR_PAD_LEFT);

        return $whole.'.'.$fraction;
    }
}
