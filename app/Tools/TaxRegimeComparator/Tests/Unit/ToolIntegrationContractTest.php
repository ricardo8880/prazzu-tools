<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\ToolIntegration\Contracts\ToolIntegrationCatalog;
use App\Tools\TaxRegimeComparator\Tool;
use Tests\TestCase;

final class ToolIntegrationContractTest extends TestCase
{
    public function test_declared_integration_contracts_are_registered_in_the_core(): void
    {
        $catalog = app(ToolIntegrationCatalog::class);
        $manifest = (new Tool)->integrations();
        $contractKeys = [...$manifest->publishes, ...$manifest->accepts];

        self::assertSame([], $contractKeys);

        foreach ($contractKeys as $contractKey) {
            self::assertNotNull(
                $catalog->find(...$this->contractIdentity($contractKey)),
                "O contrato [{$contractKey}] deve estar registrado no Core.",
            );
        }
    }

    /** @return array{string, int} */
    private function contractIdentity(string $contractKey): array
    {
        [$name, $version] = explode(':v', $contractKey, 2);

        return [$name, (int) $version];
    }
}
