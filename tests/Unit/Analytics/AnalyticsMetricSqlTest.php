<?php

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Application\Queries\AnalyticsMetricSql;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Tests\TestCase;

final class AnalyticsMetricSqlTest extends TestCase
{
    public function test_it_counts_a_logical_action_instead_of_raw_event_rows(): void
    {
        $sql = AnalyticsMetricSql::countDistinctCase(['tool.opened', 'tool.viewed'], "COALESCE(subject_slug, '')");

        $this->assertStringContainsString('COUNT(DISTINCT CASE WHEN event_name IN (?,?)', $sql);
        $this->assertStringContainsString('analytics_session_id', $sql);
        $this->assertStringContainsString('visitor_id', $sql);
        $this->assertStringContainsString('subject_slug', $sql);
        $this->assertStringContainsString('event_id', $sql);
        $this->assertStringContainsString('analytics_session_id IS NULL', $sql);
    }

    public function test_mysql_identity_uses_a_valid_concat_ws_expression(): void
    {
        if (PlatformAnalyticsEvent::query()->getConnection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('This assertion is specific to MySQL/MariaDB.');
        }

        $sql = AnalyticsMetricSql::identity("COALESCE(path, '')");

        $this->assertStringContainsString("CONCAT_WS('|', " , $sql);
        $this->assertStringNotContainsString('ooOOA', $sql);
    }
}
