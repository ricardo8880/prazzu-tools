<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Analytics;

use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use App\Http\Middleware\EnsureInternalAdministrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AnalyticsReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_screen_filters_and_exports_events(): void
    {
        PlatformAnalyticsEvent::query()->create([
            'event_id' => (string) str()->uuid(), 'event_name' => 'page.viewed', 'schema_version' => 2,
            'channel' => 'platform', 'source' => 'google', 'device_type' => 'mobile', 'region' => 'SP',
            'path' => '/blog/teste', 'metadata' => [], 'occurred_at' => now(),
        ]);

        $this->withoutMiddleware(EnsureInternalAdministrator::class)
            ->get(route('admin.analytics.reports', ['period' => '7', 'source' => 'google']))
            ->assertOk()->assertSee('Relatórios do Analytics')->assertSee('page.viewed');

        $this->withoutMiddleware(EnsureInternalAdministrator::class)
            ->get(route('admin.analytics.reports.export', ['period' => '7', 'source' => 'google', 'format' => 'csv']))
            ->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_an_analytics_report_can_be_scheduled(): void
    {
        $this->withoutMiddleware(EnsureInternalAdministrator::class)->post(route('admin.analytics.reports.schedules.store'), [
            'name' => 'Resumo semanal', 'frequency' => 'weekly', 'format' => 'pdf', 'period' => '30', 'device_type' => 'mobile',
        ])->assertRedirect();

        $this->assertDatabaseHas('analytics_report_schedules', ['name' => 'Resumo semanal', 'frequency' => 'weekly', 'is_active' => true]);
    }
}
