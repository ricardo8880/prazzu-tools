<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MarginMarkupPersistenceFlowTest extends TestCase
{
    use RefreshDatabase;

    #[CoversPlusFeature('margin-markup-calculator', 'history')]
    public function test_authenticated_user_can_list_repeat_export_and_delete_owned_history(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $run = $this->createRun($user, salePrice: '$160.00');
        $otherRun = $this->createRun($otherUser, salePrice: '$999.00');

        $this->actingAs($user)
            ->get(route('tools.margin-markup-calculator.history.index'))
            ->assertOk()
            ->assertSee('$160.00')
            ->assertDontSee('$999.00');

        $this->actingAs($user)
            ->get(route('tools.margin-markup-calculator.history.show', $run))
            ->assertOk()
            ->assertSee('Calculation details')
            ->assertSee('$160.00');

        $this->actingAs($user)
            ->get(route('tools.margin-markup-calculator.history.show', $otherRun))
            ->assertNotFound();

        $this->actingAs($user)
            ->post(route('tools.margin-markup-calculator.history.repeat', $run))
            ->assertRedirect(route('tools.margin-markup-calculator.index'))
            ->assertSessionHasInput('base_cost', '100.00');

        $this->actingAs($user)
            ->get(route('tools.margin-markup-calculator.history.pdf', $run))
            ->assertOk()
            ->assertSee('Margin, Markup & Pricing Report')
            ->assertSee('$160.00');

        $this->actingAs($user)
            ->delete(route('tools.margin-markup-calculator.history.destroy', $run))
            ->assertRedirect(route('tools.margin-markup-calculator.history.index'));

        $this->assertDatabaseMissing('tool_runs', ['id' => $run->id]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'tool_run.deleted',
            'auditable_id' => $run->id,
            'actor_id' => $user->id,
        ]);
    }

    private function createRun(User $user, string $salePrice): ToolRun
    {
        return ToolRun::query()->create([
            'user_id' => $user->id,
            'tool_slug' => 'margin-markup-calculator',
            'tool_version' => '1.0.0',
            'rule_version' => '2.0.0',
            'reference_date' => '2026-07-15',
            'status' => ToolRunStatus::Succeeded,
            'input_payload' => [
                'reference_date' => '2026-07-15',
                'base_cost' => '100.00',
                'desired_margin' => '20',
            ],
            'result_payload' => [
                'calculation_type' => 'single',
                'total_cost' => '$100.00',
                'sale_price' => $salePrice,
                'gross_profit' => '$60.00',
                'net_profit' => '$60.00',
                'taxes_amount' => '$0.00',
                'commission_amount' => '$0.00',
                'card_fees_amount' => '$0.00',
                'marketplace_fees_amount' => '$0.00',
                'margin' => '20%',
                'markup' => '60%',
                'markup_multiplier' => '1.60',
                'rule_version' => '2.0.0',
            ],
            'started_at' => now()->subSecond(),
            'finished_at' => now(),
            'expires_at' => now()->addDays(90),
        ]);
    }
}
