<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Application\Data;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\OvertimeCalculator\Domain\Data\OvertimeInput;
use InvalidArgumentException;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public string $competence,
        public string $baseSalary,
        public int $monthlyHours = 220,
        public string $overtime50Hours = '0',
        public string $overtime100Hours = '0',
        public string $customOvertimeHours = '0',
        public string $customPremium = '50',
        public string $nightClockHours = '0',
        public string $nightOvertimeHours = '0',
        public string $nightOvertimePremium = '50',
        public int $workingDays = 0,
        public int $restDays = 0,
        public bool $includeDsr = false,
        public bool $includeReflexes = false,
    ) {}

    public function toDomain(): OvertimeInput
    {
        return new OvertimeInput(
            Competence::fromString($this->competence), Money::fromDecimal($this->baseSalary), $this->monthlyHours,
            self::hours($this->overtime50Hours), self::hours($this->overtime100Hours), self::hours($this->customOvertimeHours), Percentage::fromString($this->customPremium),
            self::hours($this->nightClockHours), self::hours($this->nightOvertimeHours), Percentage::fromString($this->nightOvertimePremium),
            $this->workingDays, $this->restDays, $this->includeDsr, $this->includeReflexes,
        );
    }

    /** @return array<string, int|string|bool> */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    private static function hours(string $value): int
    {
        $normalized = trim(str_replace(',', '.', $value));
        if (!preg_match('/^\d+(?:\.\d{1,3})?$/', $normalized, $m)) throw new InvalidArgumentException('Quantidade de horas inválida.');
        [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '');
        return ((int)$whole * 1000) + (int)str_pad($fraction, 3, '0');
    }
}
