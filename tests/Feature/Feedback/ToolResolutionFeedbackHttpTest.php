<?php

declare(strict_types=1);

namespace Tests\Feature\Feedback;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Feedback\Enums\ToolResolution;
use App\Core\Feedback\Enums\ToolResolutionReason;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolResolutionFeedbackHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_confirm_that_a_tool_resolved_the_need(): void
    {
        $response = $this->postJson(route('feedback.tool.store'), [
            'feedback_kind' => 'resolution',
            'tool_slug' => 'gerador-de-contratos',
            'resolution' => ToolResolution::Yes->value,
            'path' => '/ferramentas/gerador-de-contratos',
            'url' => 'http://localhost/ferramentas/gerador-de-contratos',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('tool_resolution_feedback', [
            'tool_slug' => 'gerador-de-contratos',
            'resolution' => ToolResolution::Yes->value,
            'reason' => null,
        ]);

        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => AnalyticsEventName::ToolResolutionSubmitted->value,
            'subject_slug' => 'gerador-de-contratos',
        ]);
    }

    public function test_partial_or_negative_resolution_requires_a_reason(): void
    {
        $this->postJson(route('feedback.tool.store'), [
            'feedback_kind' => 'resolution',
            'tool_slug' => 'gerador-de-contratos',
            'resolution' => ToolResolution::Partially->value,
            'path' => '/ferramentas/gerador-de-contratos',
            'url' => 'http://localhost/ferramentas/gerador-de-contratos',
        ])->assertUnprocessable()->assertJsonValidationErrors('reason');
    }

    public function test_incomplete_resolution_persists_reason_without_calculation_payload(): void
    {
        $this->postJson(route('feedback.tool.store'), [
            'feedback_kind' => 'resolution',
            'tool_slug' => 'gerador-de-contratos',
            'resolution' => ToolResolution::No->value,
            'reason' => ToolResolutionReason::CaseNotCovered->value,
            'comment' => 'Meu cenário possui uma condição que não encontrei.',
            'path' => '/ferramentas/gerador-de-contratos',
            'url' => 'http://localhost/ferramentas/gerador-de-contratos',
        ])->assertCreated();

        $this->assertDatabaseHas('tool_resolution_feedback', [
            'tool_slug' => 'gerador-de-contratos',
            'resolution' => ToolResolution::No->value,
            'reason' => ToolResolutionReason::CaseNotCovered->value,
            'comment' => 'Meu cenário possui uma condição que não encontrei.',
        ]);
    }
}
