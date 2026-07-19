<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Feature;

use App\Models\User;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingFeeCalculation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountingFeesHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculation_is_saved_and_can_be_listed_in_history(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('tools.calculadora-de-honorarios-contabeis.calculate'), [
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
        $this->actingAs($user)->get(route('tools.calculadora-de-honorarios-contabeis.history.index'))
            ->assertOk()
            ->assertSee('Histórico de cálculos');
        $this->actingAs($user)
            ->get(route('tools.calculadora-de-honorarios-contabeis.history.export'))
            ->assertDownload('historico-honorarios-contabeis.csv');
    }

    public function test_owner_can_favorite_duplicate_and_delete_a_calculation(): void
    {
        $user = User::factory()->create();

        $calculation = AccountingFeeCalculation::query()->create([
            'user_id' => $user->getAuthIdentifier(),
            'input' => ['monthly_revenue' => '50.000,00', 'tax_regime' => 'mei'],
            'result' => ['recommended_fee' => 'R$ 500,00'],
        ]);

        $this->actingAs($user)
            ->patch(route('tools.calculadora-de-honorarios-contabeis.history.favorite', $calculation))
            ->assertRedirect();
        $this->assertTrue($calculation->fresh()->is_favorite);

        $this->actingAs($user)
            ->post(route('tools.calculadora-de-honorarios-contabeis.history.duplicate', $calculation))
            ->assertRedirect(route('tools.calculadora-de-honorarios-contabeis.index'));

        $this->actingAs($user)
            ->delete(route('tools.calculadora-de-honorarios-contabeis.history.delete', $calculation))
            ->assertRedirect();
        $this->assertDatabaseMissing('accounting_fee_calculations', ['id' => $calculation->id]);
    }

    public function test_user_cannot_change_another_users_calculation(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $calculation = AccountingFeeCalculation::query()->create([
            'user_id' => $owner->getAuthIdentifier(),
            'input' => ['monthly_revenue' => '50.000,00'],
            'result' => ['recommended_fee' => 'R$ 500,00'],
        ]);

        $this->actingAs($otherUser)
            ->patch(route('tools.calculadora-de-honorarios-contabeis.history.favorite', $calculation))
            ->assertNotFound();

        $this->actingAs($otherUser)
            ->delete(route('tools.calculadora-de-honorarios-contabeis.history.delete', $calculation))
            ->assertNotFound();

        $this->assertDatabaseHas('accounting_fee_calculations', [
            'id' => $calculation->getKey(),
            'is_favorite' => false,
        ]);
    }
}
