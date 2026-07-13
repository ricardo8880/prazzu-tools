<?php

namespace Tests\Unit\Core\Tools;

use App\Core\Tools\Contracts\ToolModule;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Exceptions\DuplicateToolException;
use App\Core\Tools\ToolRegistry;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

final class ToolRegistryTest extends TestCase
{
    public function test_registry_indexes_modules_by_manifest_slug(): void
    {
        $registry = new ToolRegistry(new Container(), [ExampleToolModule::class]);

        $this->assertTrue($registry->has('ferramenta-exemplo'));
        $this->assertSame(1, $registry->count());
        $this->assertSame('Ferramenta Exemplo', $registry->findManifest('ferramenta-exemplo')?->name);
    }

    public function test_registry_rejects_duplicate_slugs(): void
    {
        $registry = new ToolRegistry(new Container());
        $registry->register(new ExampleToolModule());

        $this->expectException(DuplicateToolException::class);

        $registry->register(new ExampleToolModule());
    }
}

final class ExampleToolModule implements ToolModule
{
    public function manifest(): ToolManifest
    {
        return new ToolManifest(
            slug: 'ferramenta-exemplo',
            name: 'Ferramenta Exemplo',
            description: 'Módulo usado exclusivamente pelos testes do registro.',
            category: ToolCategory::Other,
            icon: 'bi-tools',
            routeName: 'tools.ferramenta-exemplo.index',
        );
    }
}
