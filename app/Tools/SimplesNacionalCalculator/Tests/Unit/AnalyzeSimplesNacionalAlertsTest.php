<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Tools\SimplesNacionalCalculator\Application\Actions\AnalyzeSimplesNacionalAlerts;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\FactorRCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Rules\SimplesNacionalTaxTable;
use App\Tools\SimplesNacionalCalculator\Domain\Services\SimplesNacionalAlertAnalyzer;
use PHPUnit\Framework\TestCase;

final class AnalyzeSimplesNacionalAlertsTest extends TestCase
{
    public function test_it_warns_when_next_bracket_is_near(): void
    {
        $action = $this->action();

        $result = $action->execute([
            'annex' => 'I',
            'rbt12' => '175000.00',
            'monthly_revenue' => '15000.00',
            'monthly_growth' => '5',
        ]);

        self::assertContains('Mudança de faixa próxima', array_column($result['alerts'], 'title'));
        self::assertContains('Impacto estimado no DAS', array_column($result['alerts'], 'title'));
    }

    public function test_it_flags_factor_r_near_threshold(): void
    {
        $action = $this->action();

        $result = $action->execute([
            'annex' => 'V',
            'rbt12' => '300000.00',
            'monthly_revenue' => '25000.00',
            'payroll_12' => '81000.00',
        ]);

        self::assertContains('Fator R sensível', array_column($result['alerts'], 'title'));
    }

    private function action(): AnalyzeSimplesNacionalAlerts
    {
        $table = new SimplesNacionalTaxTable;

        return new AnalyzeSimplesNacionalAlerts(
            new SimplesNacionalAlertAnalyzer(
                new SimplesNacionalCalculator($table),
                new FactorRCalculator,
                $table,
            ),
        );
    }
}
