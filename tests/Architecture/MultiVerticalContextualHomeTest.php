<?php

namespace Tests\Architecture;

use App\Core\Acquisition\Application\Home\BuildContextualHome;
use App\Core\Verticals\Application\VerticalContext;
use ReflectionClass;
use Tests\TestCase;

final class MultiVerticalContextualHomeTest extends TestCase
{
    public function test_home_builder_depends_on_vertical_context_without_vertical_specific_classes(): void
    {
        $reflection = new ReflectionClass(BuildContextualHome::class);
        $constructor = $reflection->getConstructor();

        self::assertNotNull($constructor);
        self::assertTrue(collect($constructor->getParameters())->contains(
            static fn ($parameter): bool => (string) $parameter->getType() === VerticalContext::class,
        ));

        $forbidden = [
            app_path('Http/Controllers/Platform/HomeContabilidadeController.php'),
            app_path('Http/Controllers/Platform/HomeRhController.php'),
            app_path('Http/Controllers/Platform/HomeFinanceiroController.php'),
        ];

        foreach ($forbidden as $path) {
            self::assertFileDoesNotExist($path);
        }
    }

    public function test_home_configuration_has_global_fallback_and_accounting_reference_vertical(): void
    {
        self::assertIsArray(config('home.global'));
        self::assertIsArray(config('home.verticals.contabilidade'));
        self::assertSame(config('home.hero'), config('home.verticals.contabilidade.hero'));
        self::assertSame(config('home.cta'), config('home.verticals.contabilidade.cta'));
        self::assertNotSame(
            config('home.global.hero.description'),
            config('home.verticals.contabilidade.hero.description'),
        );
    }
}
