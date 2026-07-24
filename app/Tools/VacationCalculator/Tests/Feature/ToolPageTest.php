<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Feature;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Usage\Contracts\UsageMetrics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    use RefreshDatabase;
    public function test_tool_page_is_available_with_essential_form(): void
    {
        $this->get(route('tools.calculadora-ferias.index'))
            ->assertOk()
            ->assertSee('Calculadora de Férias')
            ->assertSee('Dados das férias')
            ->assertSee('Abono pecuniário')
            ->assertSee('Como interpretar');
    }

    public function test_valid_input_calculates_and_displays_the_result(): void
    {
        $metrics = $this->mock(UsageMetrics::class);
        $metrics->shouldReceive('record')->once();

        $analytics = $this->mock(PlatformAnalytics::class);
        $analytics->shouldReceive('track')->zeroOrMoreTimes();

        $response = $this->post(route('tools.calculadora-ferias.calculate'), [
            'monthly_salary' => '3.000,00',
            'acquisition_start_date' => '2025-01-01',
            'vacation_start_date' => '2026-07-01',
            'unjustified_absences' => 0,
            'convert_one_third_to_cash' => '1',
            'commission_average' => '0,00',
            'overtime_average' => '0,00',
            'recurring_additions' => '0,00',
            'other_deductions' => '0,00',
        ]);

        $response->assertOk()
            ->assertSee('Resultado das férias')
            ->assertSee('R$ 4.000,00')
            ->assertSee('29/06/2026');
    }

    public function test_invalid_money_and_dates_are_rejected(): void
    {
        $this->from(route('tools.calculadora-ferias.index'))
            ->post(route('tools.calculadora-ferias.calculate'), [
                'monthly_salary' => 'valor inválido',
                'acquisition_start_date' => '2026-01-01',
                'vacation_start_date' => '2025-12-01',
            ])
            ->assertRedirect(route('tools.calculadora-ferias.index'))
            ->assertSessionHasErrors(['monthly_salary', 'vacation_start_date']);
    }

    public function test_page_contains_seo_metadata(): void
    {
        $this->get(route('tools.calculadora-ferias.index'))
            ->assertOk()
            ->assertSee('<title>Calculadora de Férias — Prazzu Tools</title>', false)
            ->assertSee('Calcule férias, terço constitucional', false);
    }
}
