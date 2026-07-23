<?php

declare(strict_types=1);

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Application\Services\HistoricalAnalyticsDeduplicator;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class AnalyticsHistoryRepairTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_reports_duplicates_without_deleting_them(): void
    {
        $this->createEvent(AnalyticsEventName::PageViewed->value, 'session-a', '/inicio', now());
        $duplicate = $this->createEvent(AnalyticsEventName::PageViewed->value, 'session-a', '/inicio', now()->addSeconds(2));
        $this->createEvent(AnalyticsEventName::PageViewed->value, 'session-a', '/outra', now()->addSeconds(3));

        $result = app(HistoricalAnalyticsDeduplicator::class)->run();

        $this->assertSame(3, $result['scanned']);
        $this->assertSame(1, $result['duplicates']);
        $this->assertSame([$duplicate->id], $result['duplicate_ids']);
        $this->assertDatabaseCount('platform_analytics_events', 3);
    }

    public function test_apply_removes_only_confirmed_duplicates(): void
    {
        $this->createEvent(AnalyticsEventName::BlogPostViewed->value, 'session-a', '/blog/artigo', now(), 'blog_post', 10, 'artigo');
        $this->createEvent(AnalyticsEventName::BlogPostViewed->value, 'session-a', '/blog/artigo', now()->addSeconds(5), 'blog_post', 10, 'artigo');
        $this->createEvent(AnalyticsEventName::BlogPostViewed->value, 'session-b', '/blog/artigo', now()->addSeconds(5), 'blog_post', 10, 'artigo');
        $this->createEvent(AnalyticsEventName::BlogPostViewed->value, 'session-a', '/blog/artigo', now()->addSeconds(20), 'blog_post', 10, 'artigo');

        $result = app(HistoricalAnalyticsDeduplicator::class)->run(apply: true);

        $this->assertSame(1, $result['duplicates']);
        $this->assertSame(1, $result['deleted']);
        $this->assertDatabaseCount('platform_analytics_events', 3);
    }

    public function test_distinct_scroll_milestones_are_preserved(): void
    {
        config()->set('analytics.history_repair.event_windows.blog.scroll.measured', 30);

        $this->createEvent('blog.scroll.measured', 'session-a', '/blog/artigo', now(), metadata: ['percentage' => 25]);
        $this->createEvent('blog.scroll.measured', 'session-a', '/blog/artigo', now()->addSecond(), metadata: ['percentage' => 50]);

        $result = app(HistoricalAnalyticsDeduplicator::class)->run(apply: true);

        $this->assertSame(0, $result['duplicates']);
        $this->assertDatabaseCount('platform_analytics_events', 2);
    }

    public function test_repair_command_is_safe_by_default(): void
    {
        $this->createEvent(AnalyticsEventName::ToolOpened->value, 'session-a', '/ferramentas/teste', now(), 'tool', null, 'teste');
        $this->createEvent(AnalyticsEventName::ToolOpened->value, 'session-a', '/ferramentas/teste', now()->addSecond(), 'tool', null, 'teste');

        $this->artisan('analytics:repair-history')
            ->expectsOutputToContain('Simulação concluída')
            ->assertSuccessful();

        $this->assertDatabaseCount('platform_analytics_events', 2);
    }

    /** @param array<string,mixed> $metadata */
    private function createEvent(
        string $eventName,
        string $session,
        string $path,
        mixed $occurredAt,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?string $subjectSlug = null,
        array $metadata = [],
    ): PlatformAnalyticsEvent {
        return PlatformAnalyticsEvent::query()->create([
            'event_id' => (string) Str::uuid(),
            'event_name' => $eventName,
            'schema_version' => 1,
            'channel' => 'web',
            'session_id' => $session,
            'path' => $path,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'subject_slug' => $subjectSlug,
            'metadata' => $metadata,
            'occurred_at' => $occurredAt,
        ]);
    }
}
