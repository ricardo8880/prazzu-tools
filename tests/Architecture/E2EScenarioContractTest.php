<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Quality\E2E\Data\ToolScenario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class E2EScenarioContractTest extends TestCase
{
    #[Test]
    public function declarative_scenarios_follow_the_official_contract(): void
    {
        $config = require base_path('config/e2e_scenarios.php');
        $officialSlugs = array_column((require base_path('config/product_tools.php'))['official'], 'slug');
        $count = 0;

        foreach ($config['tools'] as $slug => $scenarios) {
            self::assertContains($slug, $officialSlugs);
            foreach ($scenarios as $scenario) {
                self::assertInstanceOf(ToolScenario::class, $scenario);
                self::assertSame($slug, $scenario->toolSlug);
                self::assertNotEmpty($scenario->steps);
                self::assertNotEmpty($scenario->expectations);
                $count++;
            }
        }

        self::assertGreaterThanOrEqual(2, $count);
    }
}
