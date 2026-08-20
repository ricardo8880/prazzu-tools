<?php

declare(strict_types=1);

namespace Tests\Architecture;

use Tests\TestCase;

final class ProblemJourneyDiscoveryExperienceTest extends TestCase
{
    public function test_problem_journey_presentation_stays_shared_and_does_not_duplicate_tool_rules(): void
    {
        $component = file_get_contents(resource_path('views/components/tools/problem-journeys.blade.php'));
        $config = require config_path('tools/discovery_journeys.php');

        self::assertIsString($component);
        self::assertStringContainsString('problem_journey', $component);
        self::assertStringNotContainsString('App\\Tools\\', $component);
        self::assertArrayHasKey('contabilidade', $config);
        self::assertArrayHasKey('rh', $config);

        foreach ($config as $journeys) {
            foreach ($journeys as $journey) {
                self::assertArrayHasKey('start_slug', $journey);
                self::assertArrayNotHasKey('steps', $journey, 'Os passos devem vir de config/tools/journeys.php, não ser duplicados.');
            }
        }
    }
}
