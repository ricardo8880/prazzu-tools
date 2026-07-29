<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Tools\Analytics;

use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class ToolAnalyticsExpansionCoverageTest extends TestCase
{
    public function test_all_official_tools_declare_an_analytics_journey(): void
    {
        $registry = $this->app->make(ToolRegistry::class);
        $modules = $registry->modules();

        self::assertCount(32, $modules);

        foreach ($modules as $slug => $module) {
            self::assertInstanceOf(HasAnalyticsJourney::class, $module, "A ferramenta [{$slug}] não declara jornada de Analytics.");
            self::assertSame($slug, $module->analyticsJourney()->toolSlug);
            self::assertNotEmpty($module->analyticsJourney()->forms);
        }
    }
}
