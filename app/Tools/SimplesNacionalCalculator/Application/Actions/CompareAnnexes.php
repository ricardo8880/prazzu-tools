<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Tools\SimplesNacionalCalculator\Domain\Data\ScenarioComparison;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;

final readonly class CompareAnnexes
{
    public function __construct(private CompareScenarios $compareScenarios) {}

    /** @param list<string> $annexes */
    public function execute(array $annexes, string $rbt12, string $monthlyRevenue): ScenarioComparison
    {
        return $this->compareScenarios->execute(array_map(
            static fn (string $annex): array => [
                'name' => TaxAnnex::from($annex)->label(),
                'annex' => $annex,
                'rbt12' => $rbt12,
                'monthly_revenue' => $monthlyRevenue,
            ],
            $annexes,
        ));
    }
}
