<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Feature;

use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingFeeCalculation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountingFeesHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculation_is_saved_and_can_be_listed_in_history(): void
    {
        $this->post(route('tools.calculadora-de-honorarios-contabeis.calculate'), [
            'monthly_revenue' => '100.000,00',
            'employees' => 3,
            'partners' => 2,
            'monthly_invoices' => 80,
            'monthly_bank_transactions' => 150,
            'tax_regime' => 'simples_nacional',
            'business_segment' => 'services',
            'complexity' => 'medium',
        ])->assertRedirect();

        $this->assertDatabaseCount('accounting_fee_calculations', 1);
        $this->get(route('tools.calculadora-de-honorarios-contabeis.history.index'))
            ->assertOk()
            ->assertSee('Histórico de cálculos');
    }

    public function test_owner_can_favorite_duplicate_share_and_delete_a_calculation(): void
    {
        $this->withSession(['accounting_fees_crm_key' => '51c7a8e6-a83f-4a3c-a310-48c453f7dbe1']);

        $calculation = AccountingFeeCalculation::query()->create([
            'session_key' => '51c7a8e6-a83f-4a3c-a310-48c453f7dbe1',
            'input' => ['monthly_revenue' => '50.000,00', 'tax_regime' => 'mei'],
            'result' => ['recommended_fee' => 'R$ 500,00'],
        ]);

        $this->patch(route('tools.calculadora-de-honorarios-contabeis.history.favorite', $calculation))->assertRedirect();
        $this->assertTrue($calculation->fresh()->is_favorite);

        $this->post(route('tools.calculadora-de-honorarios-contabeis.history.share', $calculation))->assertRedirect();
        $this->assertNotNull($calculation->fresh()->share_token);

        $this->post(route('tools.calculadora-de-honorarios-contabeis.history.duplicate', $calculation))
            ->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.index'));

        $this->delete(route('tools.calculadora-de-honorarios-contabeis.history.delete', $calculation))->assertRedirect();
        $this->assertDatabaseMissing('accounting_fee_calculations', ['id' => $calculation->id]);
    }
}
