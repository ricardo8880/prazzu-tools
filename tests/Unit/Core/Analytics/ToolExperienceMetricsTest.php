<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Analytics;

use App\Core\Analytics\Application\Queries\ToolAnalyticsQuery;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use ReflectionMethod;
use Tests\TestCase;

final class ToolExperienceMetricsTest extends TestCase
{
    public function test_session_funnel_exposes_the_real_dropoff_between_open_start_and_result(): void
    {
        $events = collect([
            $this->event(AnalyticsEventName::ToolOpened, '2026-08-10 10:00:00', session: 1, visitor: 10),
            $this->event(AnalyticsEventName::ToolOpened, '2026-08-10 10:01:00', session: 2, visitor: 10),
            $this->event(AnalyticsEventName::ToolOpened, '2026-08-10 10:02:00', session: 3, visitor: 20),
            $this->event(AnalyticsEventName::ToolStarted, '2026-08-10 10:03:00', session: 1, visitor: 10),
            $this->event(AnalyticsEventName::ToolStarted, '2026-08-10 10:04:00', session: 2, visitor: 10),
            $this->event(AnalyticsEventName::ToolResultViewed, '2026-08-10 10:05:00', session: 1, visitor: 10),
            $this->event(AnalyticsEventName::ToolAbandoned, '2026-08-10 10:06:00', session: 2, visitor: 10),
        ]);

        $metrics = $this->invoke('journeyFunnel', $events, 'session');

        self::assertSame(3, $metrics->opens);
        self::assertSame(2, $metrics->starts);
        self::assertSame(1, $metrics->results);
        self::assertSame(66.7, $metrics->start_rate);
        self::assertSame(50.0, $metrics->result_after_start_rate);
        self::assertSame(33.3, $metrics->completion_rate);
        self::assertSame(1, $metrics->abandonments);
        self::assertSame(50.0, $metrics->abandonment_rate);
    }

    public function test_people_funnel_deduplicates_multiple_sessions_from_the_same_person(): void
    {
        $events = collect([
            $this->event(AnalyticsEventName::ToolOpened, '2026-08-10 10:00:00', session: 1, visitor: 10),
            $this->event(AnalyticsEventName::ToolOpened, '2026-08-10 11:00:00', session: 2, visitor: 10),
            $this->event(AnalyticsEventName::ToolOpened, '2026-08-10 12:00:00', session: 3, visitor: 20),
            $this->event(AnalyticsEventName::ToolStarted, '2026-08-10 12:10:00', session: 2, visitor: 10),
            $this->event(AnalyticsEventName::ToolResultViewed, '2026-08-10 12:20:00', session: 2, visitor: 10),
        ]);

        $sessions = $this->invoke('journeyFunnel', $events, 'session');
        $people = $this->invoke('journeyFunnel', $events, 'person');

        self::assertSame(3, $sessions->opens);
        self::assertSame(2, $people->opens);
        self::assertSame(1, $people->starts);
        self::assertSame(1, $people->results);
        self::assertSame(50.0, $people->start_rate);
        self::assertSame(100.0, $people->result_after_start_rate);
    }

    public function test_return_requires_a_new_completed_result_on_another_day(): void
    {
        $events = collect([
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-08-10 10:00:00', user: 10),
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-08-11 09:00:00', user: 10),
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-08-10 11:00:00', visitor: 20),
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-08-10 12:00:00', visitor: 20),
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-08-12 12:00:00'),
        ]);

        $metrics = $this->invoke('retentionMetrics', $events, CarbonImmutable::parse('2026-08-12')->endOfDay());

        self::assertSame(5, $metrics->problems_solved);
        self::assertSame(2, $metrics->solvers);
        self::assertSame(1, $metrics->returning_solvers);
        self::assertSame(50.0, $metrics->return_rate);
    }

    public function test_d1_d7_and_d30_are_exact_cohort_retention_and_only_use_mature_cohorts(): void
    {
        $events = collect([
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-07-01 10:00:00', user: 10),
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-07-02 10:00:00', user: 10),
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-07-08 10:00:00', user: 10),
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-07-31 10:00:00', user: 10),
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-07-01 11:00:00', visitor: 20),
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-07-08 11:00:00', visitor: 20),
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-07-31 12:00:00', visitor: 30),
        ]);

        $metrics = $this->invoke('retentionMetrics', $events, CarbonImmutable::parse('2026-07-31')->endOfDay());

        self::assertSame(2, $metrics->d1->eligible);
        self::assertSame(1, $metrics->d1->returned);
        self::assertSame(50.0, $metrics->d1->rate);
        self::assertSame(2, $metrics->d7->eligible);
        self::assertSame(2, $metrics->d7->returned);
        self::assertSame(100.0, $metrics->d7->rate);
        self::assertSame(2, $metrics->d30->eligible);
        self::assertSame(1, $metrics->d30->returned);
        self::assertSame(50.0, $metrics->d30->rate);
    }

    public function test_immature_cohort_returns_null_instead_of_false_zero_percent(): void
    {
        $events = collect([
            $this->event(AnalyticsEventName::ToolCalculationCompleted, '2026-08-14 10:00:00', user: 10),
        ]);

        $metrics = $this->invoke('retentionMetrics', $events, CarbonImmutable::parse('2026-08-14')->endOfDay());

        self::assertSame(0, $metrics->d1->eligible);
        self::assertNull($metrics->d1->rate);
        self::assertNull($metrics->d7->rate);
        self::assertNull($metrics->d30->rate);
    }

    private function invoke(string $method, mixed ...$arguments): object
    {
        $reflection = new ReflectionMethod(ToolAnalyticsQuery::class, $method);

        return $reflection->invoke($this->app->make(ToolAnalyticsQuery::class), ...$arguments);
    }

    private function event(
        AnalyticsEventName $name,
        string $occurredAt,
        ?int $session = null,
        ?int $user = null,
        ?int $visitor = null,
    ): PlatformAnalyticsEvent {
        $event = new PlatformAnalyticsEvent([
            'event_id' => uniqid('event-', true),
            'event_name' => $name->value,
            'channel' => 'tool',
            'subject_type' => 'tool',
            'subject_slug' => 'ferramenta-teste',
            'analytics_session_id' => $session,
            'user_id' => $user,
            'visitor_id' => $visitor,
        ]);
        $event->occurred_at = CarbonImmutable::parse($occurredAt);

        return $event;
    }
}
