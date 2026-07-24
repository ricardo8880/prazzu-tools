<?php

declare(strict_types=1);

namespace Tests\Feature\Feedback;

use App\Core\Feedback\Enums\ToolFeedbackStatus;
use App\Core\Feedback\Enums\ToolFeedbackType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolFeedbackHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_tool_page_exposes_feedback_entry_in_right_sidebar(): void
    {
        $response = $this->get(route('tools.gerador-de-contratos.index'));

        $response->assertOk();
        $response->assertSee('Ajude a melhorar esta ferramenta');
        $response->assertSee('Enviar feedback');
        $response->assertSee('Gerador de Contratos');
    }

    public function test_non_tool_page_does_not_expose_tool_feedback_entry(): void
    {
        $response = $this->get(route('tools.index'));

        $response->assertOk();
        $response->assertDontSee('Ajude a melhorar esta ferramenta');
    }

    public function test_feedback_endpoint_stores_submission_through_shared_core(): void
    {
        $response = $this->postJson(route('feedback.tool.store'), [
            'tool_slug' => 'gerador-de-contratos',
            'type' => ToolFeedbackType::Problem->value,
            'message' => 'O contrato não atualizou após a edição.',
            'attempted_action' => 'Atualizar a visualização do contrato.',
            'path' => '/ferramentas/gerador-de-contratos',
            'url' => 'http://localhost/ferramentas/gerador-de-contratos',
            'route_name' => 'tools.gerador-de-contratos.index',
            'page_title' => 'Gerador de Contratos',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Obrigado! Seu feedback foi enviado para análise.');

        $this->assertDatabaseHas('tool_feedback', [
            'tool_slug' => 'gerador-de-contratos',
            'type' => ToolFeedbackType::Problem->value,
            'status' => ToolFeedbackStatus::New->value,
            'message' => 'O contrato não atualizou após a edição.',
            'attempted_action' => 'Atualizar a visualização do contrato.',
        ]);
    }

    public function test_feedback_endpoint_rejects_unregistered_tool_slug(): void
    {
        $this->postJson(route('feedback.tool.store'), [
            'tool_slug' => 'ferramenta-inexistente',
            'type' => ToolFeedbackType::Suggestion->value,
            'message' => 'Seria útil adicionar uma opção.',
            'path' => '/ferramentas/ferramenta-inexistente',
            'url' => 'http://localhost/ferramentas/ferramenta-inexistente',
        ])->assertUnprocessable()->assertJsonValidationErrors('tool_slug');
    }
}
