<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Tools\ToolRegistry;
use Tests\TestCase;

final class MultiVerticalCatalogingTest extends TestCase
{
    public function test_official_tools_declare_a_registered_vertical_consistently(): void
    {
        $registered = array_keys(config('verticals.registered', []));
        $inventory = collect(config('product_tools.official', []))->keyBy('slug');
        $manifests = collect($this->app->make(ToolRegistry::class)->manifests(false));

        self::assertCount(50, $inventory);
        self::assertCount(50, $manifests);

        foreach ($manifests as $manifest) {
            self::assertContains($manifest->vertical, $registered);
            self::assertSame($manifest->vertical, $inventory->get($manifest->slug)['vertical'] ?? null);
        }
    }

    public function test_existing_resources_are_explicitly_cataloged_in_registered_verticals(): void
    {
        $registered = array_keys(config('verticals.registered', []));
        $items = collect(config('resources.items', []));

        self::assertNotEmpty($items);

        foreach ($items as $item) {
            self::assertContains($item['vertical'] ?? null, $registered);
        }
    }
}
