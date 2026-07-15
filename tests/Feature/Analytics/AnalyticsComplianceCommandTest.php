<?php

declare(strict_types=1);

namespace Tests\Feature\Analytics;

use Tests\TestCase;

final class AnalyticsComplianceCommandTest extends TestCase
{
    public function test_analytics_catalog_and_production_code_are_compliant(): void
    {
        $this->artisan('analytics:check')
            ->expectsOutputToContain('Analytics está de acordo com o catálogo oficial de eventos.')
            ->assertSuccessful();
    }
}
