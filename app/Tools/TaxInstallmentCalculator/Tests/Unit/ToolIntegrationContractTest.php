<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Tests\Unit;

use App\Core\ToolIntegration\Contracts\ToolIntegrationCatalog;
use App\Tools\TaxInstallmentCalculator\Tool;
use Tests\TestCase;

final class ToolIntegrationContractTest extends TestCase
{
    public function test_declared_integration_contracts_are_registered_in_the_core(): void
    {
        $catalog = app(ToolIntegrationCatalog::class);
        $manifest = (new Tool())->integrations();

        if ($manifest->publishes === [] && $manifest->accepts === []) {
            self::addToAssertionCount(1);
        }

        foreach ([...$manifest->publishes, ...$manifest->accepts] as $contractKey) {
            [$name, $version] = explode(':v', $contractKey, 2);
            self::assertNotNull($catalog->find($name, (int) $version));
        }
    }
}
