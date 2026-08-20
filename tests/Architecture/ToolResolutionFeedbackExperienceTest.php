<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Feedback\Enums\ToolResolution;
use App\Core\Feedback\Enums\ToolResolutionReason;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ToolResolutionFeedbackExperienceTest extends TestCase
{
    #[Test]
    public function resolution_feedback_is_shared_and_only_revealed_after_a_result_exists(): void
    {
        $page = file_get_contents(resource_path('views/components/tools/page.blade.php'));
        $component = file_get_contents(resource_path('views/components/feedback/tool-resolution.blade.php'));

        self::assertStringContainsString('<x-feedback.tool-resolution :slug="$slug" />', $page);
        self::assertStringContainsString('data-tool-resolution-feedback', $component);
        self::assertStringContainsString('[data-analytics-result], [data-testid="tool-result"]', $component);
        self::assertStringContainsString('if (!result) return;', $component);
    }

    #[Test]
    public function resolution_contract_keeps_the_low_friction_three_choice_question(): void
    {
        self::assertSame(['yes', 'partially', 'no'], array_column(ToolResolution::cases(), 'value'));
        self::assertSame(
            ['result_unclear', 'missing_option', 'trust_concern', 'case_not_covered', 'found_error', 'other'],
            array_column(ToolResolutionReason::cases(), 'value'),
        );
        self::assertSame('tool.resolution.submitted', AnalyticsEventName::ToolResolutionSubmitted->value);
    }

    #[Test]
    public function feedback_does_not_change_tool_domain_contracts(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Platform/ToolFeedbackController.php'));
        $component = file_get_contents(resource_path('views/components/feedback/tool-resolution.blade.php'));

        self::assertStringNotContainsString('App\\Tools\\', $controller);
        self::assertStringNotContainsString('App\\Tools\\', $component);
        self::assertStringNotContainsString('Plus', $component);
    }
}
