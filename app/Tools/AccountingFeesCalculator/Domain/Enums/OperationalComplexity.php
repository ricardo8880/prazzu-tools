<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Enums;

enum OperationalComplexity: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case VeryHigh = 'very_high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::VeryHigh => 'Very high',
        };
    }

    public function multiplierBasisPoints(): int
    {
        return match ($this) {
            self::Low => 10_000,
            self::Medium => 11_500,
            self::High => 13_500,
            self::VeryHigh => 16_000,
        };
    }
}
