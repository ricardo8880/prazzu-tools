<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use App\Core\Tools\History\Enums\ToolRunStatus;
use App\Core\Tools\History\Models\ToolRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class HomeContinuityTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_home_exposes_recent_owned_tools_without_rendering_payloads(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $secret = 'NAO-DEVE-APARECER-NA-HOME';

        $salaryRun = $this->runFor($user, 'calculadora-salario-liquido', now(), ['secret' => $secret]);
        $this->runFor($user, 'calculadora-ferias', now()->subMinute());
        $this->runFor($other, 'capital-de-giro', now()->addMinute());

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Continue de onde parou')
            ->assertSee('Calculadora de Salário Líquido')
            ->assertSee('Calculadora de Férias')
            ->assertSee(url()->query(route('tools.calculadora-salario-liquido.history.index'), ['source' => 'home_continuity']), false)
            ->assertDontSee(route('tools.calculadora-salario-liquido.history.repeat', [$salaryRun->id]).'?source=home_continuity', false)
            ->assertDontSee('Capital de Giro')
            ->assertDontSee($secret);
    }

    public function test_guest_home_contains_only_a_temporary_recent_tools_shell(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-home-recent-tools', false)
            ->assertSee('Ficam somente nesta sessão.')
            ->assertDontSee('Continue de onde parou');
    }

    /** @param array<string, mixed> $input */
    private function runFor(
        User $user,
        string $toolSlug,
        mixed $finishedAt,
        array $input = ['amount' => '1000'],
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
            'result_payload' => ['total' => '900'],
            'started_at' => $finishedAt->copy()->subSecond(),
            'finished_at' => $finishedAt,
            'expires_at' => $finishedAt->copy()->addYear(),
        ]);
    }
}
