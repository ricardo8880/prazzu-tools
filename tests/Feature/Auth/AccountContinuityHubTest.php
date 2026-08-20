<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Core\Tools\History\Models\ToolRunFavorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountContinuityHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_hub_aggregates_only_owned_saved_results_and_favorites(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $salaryRun = $this->runFor($user, 'calculadora-salario-liquido', now()->subHour());
        $contractRun = $this->runFor($user, 'gerador-de-contratos', now()->subHours(2));
        $this->runFor($other, 'capital-de-giro', now());

        ToolRunFavorite::query()->create([
            'tool_run_id' => $contractRun->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('account.show'));

        $response->assertOk()
            ->assertViewHas('historyCount', 2)
            ->assertViewHas('favoriteCount', 1)
            ->assertViewHas('usedToolCount', 2)
            ->assertViewHas('continueRuns', function ($runs): bool {
                return $runs->pluck('tool_slug')->all() === [
                    'calculadora-salario-liquido',
                    'gerador-de-contratos',
                ];
            })
            ->assertViewHas('favoriteRuns', function ($runs): bool {
                return $runs->pluck('tool_slug')->all() === ['gerador-de-contratos'];
            });

        self::assertNotNull($salaryRun->id);
    }

    public function test_account_hub_reuses_existing_tool_repeat_route_when_available(): void
    {
        $user = User::factory()->create();
        $run = $this->runFor($user, 'calculadora-salario-liquido', now());

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('Refazer cálculo')
            ->assertSee(url()->query(route('tools.calculadora-salario-liquido.history.repeat', [$run->id]), ['source' => 'account_continuity']), false);
    }

    public function test_account_hub_shows_contextual_period_when_tool_provides_it(): void
    {
        $user = User::factory()->create();
        $this->runFor(
            $user,
            'calculadora-salario-liquido',
            now(),
            ['competence' => '2026-08', 'base_salary' => '5000'],
        );

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('Contexto:')
            ->assertSee('Agosto/2026');
    }

    public function test_account_hub_explains_favorites_when_history_exists_but_none_are_marked(): void
    {
        $user = User::factory()->create();
        $this->runFor($user, 'calculadora-salario-liquido', now());

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('Você ainda não marcou resultados como favoritos.');
    }

    public function test_account_hub_never_renders_saved_input_or_result_payloads(): void
    {
        $user = User::factory()->create();
        $secretInput = 'DOCUMENTO-ULTRASSECRETO-123';
        $secretResult = 'RESULTADO-PRIVADO-456';

        $this->runFor(
            $user,
            'calculadora-salario-liquido',
            now(),
            ['private_input' => $secretInput],
            ['private_result' => $secretResult],
        );

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee($secretInput)
            ->assertDontSee($secretResult);
    }

    public function test_account_hub_has_a_useful_empty_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('Meu Prazzu')
            ->assertSee('Ainda não há resultados salvos nesta conta.')
            ->assertSee('Explorar ferramentas');
    }

    /** @param array<string, mixed> $input @param array<string, mixed> $result */
    private function runFor(
        User $user,
        string $toolSlug,
        mixed $finishedAt,
        array $input = ['amount' => '1000'],
        array $result = ['total' => '900'],
    ): ToolRun {
        return ToolRun::query()->create([
            'user_id' => $user->id,
            'tool_slug' => $toolSlug,
            'tool_version' => '1.0.0',
            'schema_version' => 1,
            'rule_version' => '1.0.0',
            'reference_date' => $finishedAt->toDateString(),
            'status' => ToolRunStatus::Succeeded,
            'input_payload' => $input,
            'result_payload' => $result,
            'started_at' => $finishedAt->copy()->subSecond(),
            'finished_at' => $finishedAt,
            'expires_at' => $finishedAt->copy()->addYear(),
        ]);
    }
}
