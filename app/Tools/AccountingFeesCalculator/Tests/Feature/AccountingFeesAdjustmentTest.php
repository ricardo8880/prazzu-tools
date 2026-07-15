<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Tools\AccountingFeesCalculator\Infrastructure\Models\FeeAdjustment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountingFeesAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_adjustment_page_is_available(): void
    {
        $this->get(route('tools.calculadora-de-honorarios-contabeis.adjustments.index'))
            ->assertOk()
            ->assertSee('Reajuste de honorários');
    }

    public function test_it_calculates_and_stores_adjustment(): void
    {
        $this->post(route('tools.calculadora-de-honorarios-contabeis.adjustments.calculate'), [
            'client_name' => 'Empresa Exemplo',
            'index_type' => 'ipca',
            'reference_period' => '2026-07',
            'current_value' => '1.500,00',
            'percentage' => '4.62',
            'notes' => 'Índice acumulado do contrato.',
        ])->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.adjustments.index'));

        $adjustment = FeeAdjustment::query()->firstOrFail();
        self::assertSame(150000, $adjustment->current_value_cents);
        self::assertSame(156930, $adjustment->adjusted_value_cents);
        self::assertSame('Empresa Exemplo', $adjustment->client_name);
    }

    public function test_it_validates_adjustment_data(): void
    {
        $this->post(route('tools.calculadora-de-honorarios-contabeis.adjustments.calculate'), [])
            ->assertSessionHasErrors(['client_name', 'index_type', 'reference_period', 'current_value', 'percentage']);
    }
}
