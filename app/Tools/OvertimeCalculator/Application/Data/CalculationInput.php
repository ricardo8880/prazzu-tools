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
    public function toArray(): array { return get_object_vars($this); }

    public function hourlyMoney(): Money { return Money::fromDecimal($this->hourlyRate); }

    public function bonusMoney(): Money { return Money::fromDecimal($this->nondiscretionaryBonus); }

    public function regularThousandths(): int { return self::hours($this->regularHours); }

    public function overtimeThousandths(): int { return self::hours($this->overtimeHours); }

    public function multiplierPercent(): Percentage
    {
        $normalized = trim(str_replace(',', '.', $this->overtimeMultiplier));
        if (! preg_match('/^\d+(?:\.\d{1,3})?$/', $normalized)) {
            throw new InvalidArgumentException('Invalid overtime multiplier.');
        }
        return Percentage::fromString((string) ((float) $normalized * 100));
    }

    private static function hours(string $value): int
    {
        $normalized = trim(str_replace(',', '.', $value));
        if (! preg_match('/^\d+(?:\.\d{1,3})?$/', $normalized)) {
            throw new InvalidArgumentException('Invalid hours value.');
        }
        [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '');
        return ((int) $whole * 1000) + (int) str_pad($fraction, 3, '0');
    }
}
