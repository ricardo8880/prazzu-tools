<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;
use InvalidArgumentException;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $competence,
        public string $hourlyRate,
        public string $regularHours,
        public string $overtimeHours,
        public string $overtimeMultiplier = '1.5',
        public string $nondiscretionaryBonus = '0',
    ) {}

    /** @return array<string,string> */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function hourlyMoney(): Money
    {
        return Money::fromDecimal($this->hourlyRate);
    }

    public function bonusMoney(): Money
    {
        return Money::fromDecimal($this->nondiscretionaryBonus);
    }

    public function regularThousandths(): int
    {
        return self::decimalThousandths($this->regularHours, 'hours');
    }

    public function overtimeThousandths(): int
    {
        return self::decimalThousandths($this->overtimeHours, 'hours');
    }

    public function multiplierThousandths(): int
    {
        $value = self::decimalThousandths($this->overtimeMultiplier, 'overtime multiplier');
        if ($value < 1_000 || $value > 10_000) {
            throw new InvalidArgumentException('Overtime multiplier must be between 1.0 and 10.0.');
        }

        return $value;
    }

    public function multiplierPercent(): Percentage
    {
        return Percentage::fromString(self::percentFromMultiplierThousandths($this->multiplierThousandths()));
    }

    public function premiumPercent(): Percentage
    {
        return Percentage::fromString(self::percentFromMultiplierThousandths($this->multiplierThousandths() - 1_000));
    }

    private static function decimalThousandths(string $value, string $label): int
    {
        $normalized = trim(str_replace(',', '.', $value));
        if (! preg_match('/^\d+(?:\.\d{1,3})?$/', $normalized)) {
            throw new InvalidArgumentException("Invalid {$label} value.");
        }

        [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '');

        return ((int) $whole * 1_000) + (int) str_pad($fraction, 3, '0');
    }

    private static function percentFromMultiplierThousandths(int $thousandths): string
    {
        $hundredthsOfPercent = $thousandths * 10;
        $whole = intdiv($hundredthsOfPercent, 100);
        $fraction = str_pad((string) ($hundredthsOfPercent % 100), 2, '0', STR_PAD_LEFT);

        return rtrim(rtrim($whole.'.'.$fraction, '0'), '.');
    }
}
