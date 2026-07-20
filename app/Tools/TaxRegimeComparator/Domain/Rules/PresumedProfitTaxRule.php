<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Rules;

use App\Core\Money\Percentage;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class PresumedProfitTaxRule
{
    public const VERSION = '2026.1.0';

    public const VALID_FROM = '1996-01-01';

    public function supports(DateTimeImmutable $referenceDate, BusinessActivity $activity, int $revenueLastTwelveMonthsMinor): bool
    {
        if ($referenceDate < new DateTimeImmutable(self::VALID_FROM) || $activity === BusinessActivity::Mixed) {
            return false;
        }

        if ($revenueLastTwelveMonthsMinor > 7_800_000_000) {
            return false;
        }

        return ! ($referenceDate >= new DateTimeImmutable('2026-01-01')
            && $revenueLastTwelveMonthsMinor > 500_000_000);
    }

    public function irpjPresumption(BusinessActivity $activity): Percentage
    {
        return match ($activity) {
            BusinessActivity::Commerce, BusinessActivity::Industry => Percentage::fromString('8'),
            BusinessActivity::Services, BusinessActivity::AccountingServices => Percentage::fromString('32'),
            BusinessActivity::Mixed => throw new InvalidArgumentException('Atividades mistas exigem receitas segregadas.'),
        };
    }

    public function csllPresumption(BusinessActivity $activity): Percentage
    {
        return match ($activity) {
            BusinessActivity::Commerce, BusinessActivity::Industry => Percentage::fromString('12'),
            BusinessActivity::Services, BusinessActivity::AccountingServices => Percentage::fromString('32'),
            BusinessActivity::Mixed => throw new InvalidArgumentException('Atividades mistas exigem receitas segregadas.'),
        };
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
        return Percentage::fromString('0.65');
    }

    public function cofinsRate(): Percentage
    {
        return Percentage::fromString('3');
    }
}
