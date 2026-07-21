<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums;

use InvalidArgumentException;

enum CompanyRegime: string
{
    case SimplesOutsideAnnexIV = 'simples_outside_annex_iv';
    case SimplesAnnexIV = 'simples_annex_iv';
    case PresumedProfit = 'presumed_profit';
    case ActualProfit = 'actual_profit';

    public static function fromInput(string $value): self
    {
        return self::tryFrom($value) ?? throw new InvalidArgumentException('Enquadramento empresarial não suportado.');
    }

    public function employerContributionApplies(): bool
    {
        return $this !== self::SimplesOutsideAnnexIV;
    }
}
