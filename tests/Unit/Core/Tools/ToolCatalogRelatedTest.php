<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Tools;

use App\Core\Tools\ToolCatalog;
use Tests\TestCase;

final class ToolCatalogRelatedTest extends TestCase
{
    public function test_related_tools_come_from_registry_and_never_include_the_current_tool(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);
        $related = $catalog->related('simulador-fator-r');

        self::assertNotEmpty($related);
        self::assertNotContains('simulador-fator-r', $related->pluck('slug')->all());
        self::assertLessThanOrEqual(4, $related->count());

        foreach ($related as $tool) {
            self::assertNotNull($catalog->find($tool['slug']));
        }
    }

    public function test_unknown_tool_has_no_related_results(): void
    {
        self::assertTrue(
            $this->app->make(ToolCatalog::class)->related('ferramenta-inexistente')->isEmpty(),
        );
    }
}
