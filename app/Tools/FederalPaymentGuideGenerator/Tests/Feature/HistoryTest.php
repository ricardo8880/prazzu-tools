<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Feature;

use App\Core\Dates\ReferenceDate;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Models\User;
use App\Tools\FederalPaymentGuideGenerator\Tool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class HistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_persistent_history(): void
    {
        $this->get(route('tools.gerador-darf-gps.history.index'))->assertRedirect();
    }

    public function test_owner_can_view_favorite_repeat_and_delete_a_run(): void
    {
        $user = User::factory()->create();
        $tool = app(Tool::class);
        $recorder = app(ToolRunRecorder::class);
        $run = $recorder->start($tool, new RuleVersion('2026.1.0'), ReferenceDate::fromString('2026-07-21'), [
            'guide_type' => 'darf', 'revenue_code' => '0561', 'principal' => '1000.00', 'due_date' => '2026-07-10', 'payment_date' => '2026-07-21', 'selic_accumulated_percent' => '1',
        ], $user->id);
        $recorder->succeed($run, ['amounts' => ['principal' => 'R$ 1.000,00', 'total' => 'R$ 1.043,00']]);

        $this->actingAs($user)->get(route('tools.gerador-darf-gps.history.index'))->assertOk()->assertSee('R$ 1.043,00');
        $this->actingAs($user)->patch(route('tools.gerador-darf-gps.history.favorite', $run->id))->assertRedirect();
        $this->assertDatabaseHas('tool_run_favorites', ['tool_run_id' => $run->id, 'user_id' => $user->id]);
        $this->actingAs($user)->post(route('tools.gerador-darf-gps.history.repeat', $run->id))->assertRedirect(route('tools.gerador-darf-gps.index'));
        $this->actingAs($user)->get(route('tools.gerador-darf-gps.history.export', [$run->id, 'json']))
            ->assertOk()
            ->assertHeader('content-disposition');
        $this->actingAs($user)->delete(route('tools.gerador-darf-gps.history.destroy', $run->id))->assertRedirect();
    }
}
