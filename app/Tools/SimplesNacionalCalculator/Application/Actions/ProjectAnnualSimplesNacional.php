<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Domain\Services\AnnualSimplesProjection;

final readonly class ProjectAnnualSimplesNacional
{
    public function __construct(private AnnualSimplesProjection $projection) {}

    /** @return array{months:list<array<string, int|string>>, total_revenue:string, total_das:string} */
    public function execute(string $annex, string $monthlyRevenue, string $monthlyGrowth): array
    {
        return $this->projection->project(
            TaxAnnex::from($annex),
            Money::fromDecimal($monthlyRevenue),
            Percentage::fromString($monthlyGrowth),
        )->toArray();
    }
}
