<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Models\User;
use App\Tools\SimplesNacionalCalculator\Tool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PlusHandlersTest extends TestCase
{
    use RefreshDatabase;

    #[CoversPlusFeature('calculadora-simples-nacional', 'alerts')]
    public function test_alerts_route_executes_the_alert_analysis_handler(): void
    {
        $this->from(route('tools.calculadora-simples-nacional.index'))
            ->post(route('tools.calculadora-simples-nacional.plus.alerts'), [
                'annex' => 'I',
                'rbt12' => '175.000,00',
                'monthly_revenue' => '15.000,00',
            ])
            ->assertOk()
            ->assertViewHas('alertsAnalysis', static fn (array $analysis): bool => count($analysis['alerts']) > 0
                && array_key_exists('warning', $analysis['summary'])
            )
            ->assertSee('Mudança de faixa próxima');
    }

    #[CoversPlusFeature('calculadora-simples-nacional', 'compare_scenarios')]
    public function test_compares_multiple_named_scenarios(): void
    {
        $this->post(route('tools.calculadora-simples-nacional.plus.compare-scenarios'), [
            'scenarios' => [
                ['name' => 'Atual', 'annex' => 'I', 'rbt12' => '180.000,00', 'monthly_revenue' => '15.000,00'],
                ['name' => 'Crescimento', 'annex' => 'I', 'rbt12' => '360.000,00', 'monthly_revenue' => '30.000,00'],
            ],
        ])->assertOk()->assertSee('Atual')->assertSee('Crescimento');
    }

    #[CoversPlusFeature('calculadora-simples-nacional', 'compare_annexes')]
    public function test_compares_two_annexes_for_the_same_revenue(): void
    {
        $this->post(route('tools.calculadora-simples-nacional.plus.compare-annexes'), [
            'annexes' => ['III', 'V'], 'rbt12' => '360.000,00', 'monthly_revenue' => '30.000,00',
        ])->assertOk()->assertSee('Anexo III')->assertSee('Anexo V');
    }

    #[CoversPlusFeature('calculadora-simples-nacional', 'annual_projection')]
    public function test_projects_twelve_months_with_growth(): void
    {
        $this->post(route('tools.calculadora-simples-nacional.plus.project'), [
            'annex' => 'I', 'monthly_revenue' => '15.000,00', 'monthly_growth' => 2,
        ])->assertOk()->assertViewHas('annualProjection', static fn (array $projection): bool => count($projection['months']) === 12);
    }

    #[CoversPlusFeature('calculadora-simples-nacional', 'monthly_history')]
    public function test_authenticated_user_can_save_and_list_a_calculation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('tools.calculadora-simples-nacional.index'))
            ->post(route('tools.calculadora-simples-nacional.plus.history.store'), $this->historyPayload())
            ->assertRedirect(route('tools.calculadora-simples-nacional.index'))
            ->assertSessionHas('history_success');

        $entries = app(ToolRunHistory::class)->recentSucceeded(
            Tool::SLUG,
            (int) $user->getAuthIdentifier(),
        );

        self::assertCount(1, $entries);
        self::assertSame('2026-07', $entries[0]->input['reference_month']);
        self::assertSame('I', $entries[0]->input['annex']);
        self::assertSame('R$ 15.000,00', $entries[0]->result['monthly_revenue']);
        self::assertSame('R$ 600,00', $entries[0]->result['estimated_das']);
        self::assertArrayNotHasKey('company_name', $entries[0]->input);

        $this->actingAs($user)
            ->get(route('tools.calculadora-simples-nacional.index'))
            ->assertOk()
            ->assertSee('07/2026')
            ->assertDontSee('Empresa Teste');
    }

    public function test_user_can_delete_only_their_own_calculation(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($owner)
            ->post(route('tools.calculadora-simples-nacional.plus.history.store'), $this->historyPayload());

        $calculation = app(ToolRunHistory::class)->recentSucceeded(
            Tool::SLUG,
            (int) $owner->getAuthIdentifier(),
        )[0];

        $this->actingAs($otherUser)
            ->delete(route('tools.calculadora-simples-nacional.plus.history.destroy', $calculation->id))
            ->assertNotFound();

        self::assertCount(1, app(ToolRunHistory::class)->recentSucceeded(
            Tool::SLUG,
            (int) $owner->getAuthIdentifier(),
        ));

        $this->actingAs($owner)
            ->from(route('tools.calculadora-simples-nacional.index'))
            ->delete(route('tools.calculadora-simples-nacional.plus.history.destroy', $calculation->id))
            ->assertRedirect(route('tools.calculadora-simples-nacional.index'))
            ->assertSessionHas('history_success');

        self::assertSame([], app(ToolRunHistory::class)->recentSucceeded(
            Tool::SLUG,
            (int) $owner->getAuthIdentifier(),
        ));
    }

    /** @return array<string, string> */
    private function historyPayload(): array
    {
        return [
            'reference_month' => '2026-07',
            'annex' => 'I',
            'rbt12' => '180.000,00',
            'monthly_revenue' => '15.000,00',
        ];
    }
}
