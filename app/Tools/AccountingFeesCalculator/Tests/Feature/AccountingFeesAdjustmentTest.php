<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Feature;

use App\Models\User;
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
        $response = $this->actingAs(User::factory()->create())
            ->post(route('tools.calculadora-de-honorarios-contabeis.adjustments.calculate'), [
                'client_name' => 'Empresa Exemplo',
                'index_type' => 'ipca',
                'reference_period' => '2026-07',
                'current_value' => '1.500,00',
                'percentage' => '4.62',
                'notes' => 'Índice acumulado do contrato.',
            ]);

        $response
            ->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.adjustments.index'))
            ->assertSessionHas('adjustment_result.percentage', '4.62')
            ->assertSessionHas('adjustment_result.difference_cents', 6930);

        $adjustment = FeeAdjustment::query()->firstOrFail();
        self::assertSame(150000, $adjustment->current_value_cents);
        self::assertSame(156930, $adjustment->adjusted_value_cents);
        self::assertSame('4.6200', $adjustment->percentage);
        self::assertSame('Empresa Exemplo', $adjustment->client_name);
    }

    public function test_it_validates_adjustment_data(): void
    {
        $this->post(route('tools.calculadora-de-honorarios-contabeis.adjustments.calculate'), [])
            ->assertSessionHasErrors(['client_name', 'index_type', 'reference_period', 'current_value', 'percentage']);
    }

    public function test_only_the_owner_can_delete_an_adjustment(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $adjustment = FeeAdjustment::query()->create([
            'user_id' => $owner->getAuthIdentifier(),
            'client_name' => 'Empresa Exemplo',
            'index_type' => 'ipca',
            'reference_period' => '2026-07',
            'percentage' => '4.6200',
            'current_value_cents' => 150000,
            'difference_cents' => 6930,
            'adjusted_value_cents' => 156930,
        ]);

        $this->actingAs($otherUser)
            ->delete(route('tools.calculadora-de-honorarios-contabeis.adjustments.delete', $adjustment))
            ->assertNotFound();

        $this->actingAs($owner)
            ->delete(route('tools.calculadora-de-honorarios-contabeis.adjustments.delete', $adjustment))
            ->assertRedirect();

        $this->assertDatabaseMissing('accounting_fee_adjustments', ['id' => $adjustment->getKey()]);
    }
}
