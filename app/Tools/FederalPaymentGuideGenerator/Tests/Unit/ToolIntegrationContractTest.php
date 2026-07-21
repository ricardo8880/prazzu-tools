<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Unit;

use App\Core\ToolIntegration\Contracts\ToolIntegrationCatalog;
use App\Tools\FederalPaymentGuideGenerator\Tool;
use Tests\TestCase;

final class ToolIntegrationContractTest extends TestCase
{
    public function test_declared_integration_contracts_are_registered_in_the_core(): void
    {
        $catalog = app(ToolIntegrationCatalog::class);

        foreach ([...(new Tool)->integrations()->publishes, ...(new Tool)->integrations()->accepts] as $contractKey) {
            [$name, $version] = explode(':v', $contractKey, 2);
            self::assertNotNull($catalog->find($name, (int) $version));
        }

        self::addToAssertionCount(1);
    }
}
