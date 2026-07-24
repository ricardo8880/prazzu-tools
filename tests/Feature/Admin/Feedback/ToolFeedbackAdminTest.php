<?php

namespace Tests\Feature\Admin\Feedback;

use App\Core\Access\Enums\AccountRole;
use App\Core\Feedback\Enums\ToolFeedbackStatus;
use App\Core\Feedback\Enums\ToolFeedbackType;
use App\Core\Feedback\Models\ToolFeedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolFeedbackAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_administrator_can_list_and_read_tool_feedback(): void
    {
        $this->actingAs(User::factory()->create(['role' => AccountRole::Administrator]));
        $feedback = $this->feedback();

        $this->get(route('admin.feedback.tools.index'))
            ->assertOk()
            ->assertSee('Feedback das ferramentas')
            ->assertSee('Gerador de Contratos')
            ->assertSee('O botão de gerar não respondeu.');

        $this->get(route('admin.feedback.tools.show', $feedback))
            ->assertOk()
            ->assertSee('O botão de gerar não respondeu.')
            ->assertSee('Preencher o contrato de serviços.');
    }

    public function test_internal_administrator_can_filter_feedback(): void
    {
        $this->actingAs(User::factory()->create(['role' => AccountRole::Administrator]));
        $this->feedback();
        $this->feedback([
            'tool_slug' => 'outra-ferramenta',
            'tool_name' => 'Outra Ferramenta',
            'type' => ToolFeedbackType::Suggestion,
            'message' => 'Outra mensagem.',
        ]);

        $this->get(route('admin.feedback.tools.index', [
            'tool' => 'gerador-de-contratos',
            'type' => ToolFeedbackType::Problem->value,
            'status' => ToolFeedbackStatus::New->value,
        ]))
            ->assertOk()
            ->assertSee('O botão de gerar não respondeu.')
            ->assertDontSee('Outra mensagem.');
    }

    public function test_internal_administrator_can_change_feedback_status(): void
    {
        $this->actingAs(User::factory()->create(['role' => AccountRole::Administrator]));
        $feedback = $this->feedback();

        $this->patch(route('admin.feedback.tools.status', $feedback), [
            'status' => ToolFeedbackStatus::InReview->value,
        ])->assertSessionHas('status');

        $feedback->refresh();
        $this->assertSame(ToolFeedbackStatus::InReview, $feedback->status);
        $this->assertNotNull($feedback->reviewed_at);
    }

    public function test_returning_feedback_to_new_clears_reviewed_at(): void
    {
        $this->actingAs(User::factory()->create(['role' => AccountRole::Administrator]));
        $feedback = $this->feedback([
            'status' => ToolFeedbackStatus::InReview,
            'reviewed_at' => now(),
        ]);

        $this->patch(route('admin.feedback.tools.status', $feedback), [
            'status' => ToolFeedbackStatus::New->value,
        ]);

        $feedback->refresh();
        $this->assertSame(ToolFeedbackStatus::New, $feedback->status);
        $this->assertNull($feedback->reviewed_at);
    }

    public function test_regular_user_cannot_access_tool_feedback_administration(): void
    {
        $this->actingAs(User::factory()->create(['role' => AccountRole::User]));
        $feedback = $this->feedback();

        $this->get(route('admin.feedback.tools.index'))->assertForbidden();
        $this->get(route('admin.feedback.tools.show', $feedback))->assertForbidden();
        $this->patch(route('admin.feedback.tools.status', $feedback), [
            'status' => ToolFeedbackStatus::InReview->value,
        ])->assertForbidden();
    }

    private function feedback(array $overrides = []): ToolFeedback
    {
        return ToolFeedback::query()->create(array_merge([
            'session_id' => 'test-session',
            'tool_slug' => 'gerador-de-contratos',
            'tool_name' => 'Gerador de Contratos',
            'tool_version' => '1.0.0',
            'type' => ToolFeedbackType::Problem,
            'status' => ToolFeedbackStatus::New,
            'message' => 'O botão de gerar não respondeu.',
            'attempted_action' => 'Preencher o contrato de serviços.',
            'path' => '/ferramentas/gerador-de-contratos',
            'url' => 'https://example.test/ferramentas/gerador-de-contratos',
            'context' => ['route_name' => 'tools.gerador-de-contratos.index'],
        ], $overrides));
    }
}
