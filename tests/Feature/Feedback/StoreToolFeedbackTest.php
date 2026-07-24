<?php

declare(strict_types=1);

namespace Tests\Feature\Feedback;

use App\Core\Feedback\Application\StoreToolFeedback;
use App\Core\Feedback\Data\ToolFeedbackSubmission;
use App\Core\Feedback\Enums\ToolFeedbackStatus;
use App\Core\Feedback\Enums\ToolFeedbackType;
use App\Core\Feedback\Models\ToolFeedback;
use App\Core\Tools\ToolRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

final class StoreToolFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_feedback_with_registered_tool_snapshot_and_default_status(): void
    {
        $manifest = app(ToolRegistry::class)->manifests(false)[0];

        $feedback = app(StoreToolFeedback::class)->execute(new ToolFeedbackSubmission(
            toolSlug: $manifest->slug,
            type: ToolFeedbackType::Problem,
            message: '  Ao executar a ferramenta o resultado não apareceu.  ',
            attemptedAction: '  Processar os dados preenchidos.  ',
            path: '/ferramentas/'.$manifest->slug,
            url: 'http://localhost/ferramentas/'.$manifest->slug,
            sessionId: 'session-test',
            userAgent: 'PHPUnit',
            context: ['source' => 'right-sidebar'],
        ));

        self::assertSame($manifest->slug, $feedback->tool_slug);
        self::assertSame($manifest->name, $feedback->tool_name);
        self::assertSame($manifest->version, $feedback->tool_version);
        self::assertSame(ToolFeedbackType::Problem, $feedback->type);
        self::assertSame(ToolFeedbackStatus::New, $feedback->status);
        self::assertSame('Ao executar a ferramenta o resultado não apareceu.', $feedback->message);
        self::assertSame('Processar os dados preenchidos.', $feedback->attempted_action);
        self::assertSame(['source' => 'right-sidebar'], $feedback->context);
        self::assertNull($feedback->reviewed_at);

        $this->assertDatabaseHas('tool_feedback', [
            'tool_slug' => $manifest->slug,
            'type' => ToolFeedbackType::Problem->value,
            'status' => ToolFeedbackStatus::New->value,
        ]);
    }

    public function test_it_refuses_feedback_for_an_unregistered_tool(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A ferramenta [ferramenta-inexistente] não está registrada.');

        app(StoreToolFeedback::class)->execute(new ToolFeedbackSubmission(
            toolSlug: 'ferramenta-inexistente',
            type: ToolFeedbackType::Suggestion,
            message: 'Seria útil adicionar uma opção.',
            attemptedAction: null,
            path: '/ferramentas/ferramenta-inexistente',
            url: 'http://localhost/ferramentas/ferramenta-inexistente',
        ));
    }
}
