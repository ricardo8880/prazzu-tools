<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Tests\Unit;

use App\Tools\ProLaboreProfitDistributionCalculator\Application\Actions\SimulateScenarios;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services\ProfitDistributionCalculator;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services\ProLaboreCalculator;
use PHPUnit\Framework\TestCase;

final class SimulateScenariosTest extends TestCase
{
    public function test_it_consolidates_multiple_partners_periods_and_scenarios(): void
    {
        $action = new SimulateScenarios(new ProLaboreCalculator, new ProfitDistributionCalculator);
        $period = static fn (string $competence, string $grossA, string $grossB): array => [
            'competence' => $competence,
            'company_regime' => 'simples_outside_annex_iv',
            'accounting_profit' => '20000.00',
            'accumulated_losses' => '0',
            'reserves_and_unavailable_amounts' => '0',
            'adjustments' => '0',
            'prior_distributions' => '0',
            'intended_distribution' => '10000',
            'partners' => [
                ['label' => 'A', 'ownership_percentage' => '60', 'gross_pro_labore' => $grossA, 'dependents' => 0, 'other_official_social_security' => '0'],
                ['label' => 'B', 'ownership_percentage' => '40', 'gross_pro_labore' => $grossB, 'dependents' => 0, 'other_official_social_security' => '0'],
            ],
        ];

        $result = $action->execute(['scenarios' => [
            ['name' => 'Base', 'periods' => [$period('2026-01', '5000', '3000'), $period('2026-02', '5000', '3000')]],
            ['name' => 'Alternativo', 'periods' => [$period('2026-01', '6000', '4000')]],
        ]]);

        self::assertCount(2, $result['scenarios']);
        self::assertCount(2, $result['scenarios'][0]['periods']);
        self::assertCount(2, $result['scenarios'][0]['partner_totals']);
        self::assertSame(0, $result['comparison'][0]['difference_received_minor']);
        self::assertNotSame(0, $result['comparison'][1]['difference_received_minor']);
    }
}
