<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MarginMarkupToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_tool_page_is_available(): void
    {
        $this->get(route('tools.calculadora-margem-markup.index'))
            ->assertOk()
            ->assertSee('Calculadora de Margem e Markup');
    }

    public function test_calculation_returns_expected_result(): void
    {
        $response = $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.calculate'), [
                'reference_date' => '2026-07-13',
                'base_cost' => '100,00',
                'additional_costs' => '20,00',
                'desired_margin' => '25',
            ]);

        $response
            ->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHas('calculation_result', static function (array $result): bool {
                return $result['sale_price'] === 'R$ 160,00'
                    && $result['profit'] === 'R$ 40,00'
                    && $result['rule_version'] === '1.0.0';
            });

        $this->assertDatabaseCount('tool_runs', 0);
        $this->assertDatabaseHas('tool_usage_events', [
            'tool_slug' => 'calculadora-margem-markup',
            'event' => 'calculated',
        ]);
    }

    public function test_export_reuses_calculation_validation(): void
    {
        $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.export'), [
                'reference_date' => '2026-07-13',
                'base_cost' => 'valor-invalido',
                'additional_costs' => '0,00',
                'desired_margin' => '25',
            ])
            ->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHasErrors('base_cost');
    }

    public function test_export_returns_csv_and_records_metric(): void
    {
        $response = $this->post(route('tools.calculadora-margem-markup.export'), [
            'reference_date' => '2026-07-13',
            'base_cost' => '100,00',
            'additional_costs' => '20,00',
            'desired_margin' => '25',
        ]);

        $response
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertDownload('margem-markup.csv');

        $this->assertDatabaseHas('tool_usage_events', [
            'tool_slug' => 'calculadora-margem-markup',
            'event' => 'exported',
        ]);
    }

    public function test_zero_total_cost_returns_validation_error_instead_of_server_error(): void
    {
        $this->from(route('tools.calculadora-margem-markup.index'))
            ->post(route('tools.calculadora-margem-markup.calculate'), [
                'reference_date' => '2026-07-13',
                'base_cost' => '0,00',
                'additional_costs' => '0,00',
                'desired_margin' => '25',
            ])
            ->assertRedirect(route('tools.calculadora-margem-markup.index'))
            ->assertSessionHasErrors('base_cost');
    }
}
