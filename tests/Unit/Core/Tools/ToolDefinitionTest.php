<?php

namespace Tests\Unit\Core\Tools;

use App\Core\Tools\Data\ToolFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToolDefinitionTest extends TestCase
{
    public function test_manifest_accepts_the_official_tool_contract(): void
    {
        $manifest = new ToolManifest(
            slug: 'calculadora-exemplo',
            name: 'Calculadora Exemplo',
            description: 'Uma ferramenta usada para validar o contrato.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.calculadora-exemplo.index',
            version: '1.2.0',
            access: ToolAccess::Free,
            status: ToolStatus::Beta,
            position: 20,
            featured: true,
            keywords: ['cálculo', 'exemplo'],
            features: [
                new ToolFeature('calculate', 'Cálculo completo', ToolFeatureTier::Essential),
                new ToolFeature('scenarios', 'Cenários avançados', ToolFeatureTier::Plus),
            ],
        );

        $this->assertSame('calculadora-exemplo', $manifest->slug);
        $this->assertSame(ToolAccess::Free, $manifest->access);
        $this->assertSame(ToolFeatureTier::Essential, $manifest->feature('calculate')?->tier);
        $this->assertSame(ToolFeatureTier::Plus, $manifest->feature('scenarios')?->tier);
        $this->assertSame(ToolStatus::Beta, $manifest->status);
        $this->assertTrue($manifest->featured);
    }

    public function test_manifest_rejects_an_invalid_slug(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ToolManifest(
            slug: 'Calculadora Exemplo',
            name: 'Calculadora Exemplo',
            description: 'Descrição válida.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'tools.calculadora-exemplo.index',
        );
    }

    public function test_manifest_rejects_a_route_outside_the_tool_namespace(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ToolManifest(
            slug: 'calculadora-exemplo',
            name: 'Calculadora Exemplo',
            description: 'Descrição válida.',
            category: ToolCategory::Calculators,
            icon: 'bi-calculator',
            routeName: 'calculadora.index',
        );
    }
}
