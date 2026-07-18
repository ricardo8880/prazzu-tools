<?php

declare(strict_types=1);

namespace Tests\Feature\Analytics;

use App\Core\Analytics\Contracts\AnalyticsContextResolver;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Analytics\Infrastructure\Persistence\AnalyticsSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class AnalyticsSchemaResilienceTest extends TestCase
{
    public function test_global_analytics_is_a_safe_no_op_before_its_schema_exists(): void
    {
        config()->set('analytics.enabled', true);

        self::assertFalse(Schema::hasTable('analytics_visitors'));
        self::assertFalse(Schema::hasTable('analytics_sessions'));
        self::assertFalse(Schema::hasTable('platform_analytics_events'));

        $this->get(route('tools.validador-de-cnpj.index'))
            ->assertOk()
            ->assertSee('Validador Inteligente de CNPJ, CPF e IE');
    }

    public function test_context_and_event_persistence_are_safe_no_ops_without_the_schema(): void
    {
        config()->set('analytics.enabled', true);
        $request = Request::create('/ferramentas/validador-de-cnpj');

        $context = $this->app->make(AnalyticsContextResolver::class)->resolve($request);

        self::assertNull($context->visitorId);
        self::assertNull($context->analyticsSessionId);

        $this->app->make(PlatformAnalytics::class)->track(
            AnalyticsEvent::make(AnalyticsEventName::PageViewed->value, 'platform'),
            $request,
        );

        self::assertFalse(Schema::hasTable('platform_analytics_events'));
    }

    public function test_partially_migrated_analytics_tables_are_not_considered_ready(): void
    {
        foreach (['analytics_visitors', 'analytics_sessions', 'platform_analytics_events'] as $table) {
            Schema::create($table, static function (Blueprint $blueprint): void {
                $blueprint->id();
            });
        }

        self::assertFalse($this->app->make(AnalyticsSchema::class)->isReady());
    }
}
