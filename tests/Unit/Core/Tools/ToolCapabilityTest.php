<?php

namespace Tests\Unit\Core\Tools;

use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToolCapabilityTest extends TestCase
{
    public function test_manifest_serializes_and_queries_capabilities(): void
    {
        $manifest = new ToolManifest(
            slug: 'ferramenta-teste',
            name: 'Ferramenta teste',
            description: 'Descrição da ferramenta teste.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.ferramenta-teste.index',
            features: [new ToolFeature('calculate', 'Calcular', ToolFeatureTier::Essential)],
            capabilities: [ToolCapability::History],
        );

        self::assertTrue($manifest->hasCapability(ToolCapability::History));
        self::assertSame(['history'], $manifest->toArray()['capabilities']);
        self::assertTrue(ToolManifest::fromArray($manifest->toArray())->hasCapability(ToolCapability::History));
    }

    public function test_manifest_rejects_duplicate_capabilities(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ToolManifest(
            slug: 'ferramenta-teste',
            name: 'Ferramenta teste',
            description: 'Descrição da ferramenta teste.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.ferramenta-teste.index',
            features: [new ToolFeature('calculate', 'Calcular', ToolFeatureTier::Essential)],
            capabilities: [ToolCapability::History, ToolCapability::History],
        );
    }
}
