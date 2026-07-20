<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Rules;

use App\Core\Money\Percentage;
use DateTimeImmutable;

final readonly class ActualProfitTaxRule
{
    public const VERSION = '1.0.0';

    public const VALID_FROM = '2004-02-01';

    public const VALID_UNTIL = '2026-12-31';

    public function supports(DateTimeImmutable $referenceDate): bool
    {
        $date = $referenceDate->format('Y-m-d');

        return $date >= self::VALID_FROM && $date <= self::VALID_UNTIL;
    }

    public function irpjRate(): Percentage
    {
        return Percentage::fromString('15');
    }

    public function irpjAdditionalRate(): Percentage
    {
        return Percentage::fromString('10');
    }

    public function csllRate(): Percentage
    {
        return Percentage::fromString('9');
    }

    public function pisRate(): Percentage
    {
        return Percentage::fromString('1.65');
    }

    public function cofinsRate(): Percentage
    {
        return Percentage::fromString('7.6');
    }
}
