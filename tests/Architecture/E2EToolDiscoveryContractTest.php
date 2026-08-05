<?php

namespace Tests\Architecture;

use Tests\TestCase;

final class E2EToolDiscoveryContractTest extends TestCase
{
    public function test_catalog_exporter_and_universal_smoke_are_registered(): void
    {
        foreach ([
            'scripts/e2e-tool-catalog.php',
            'tests/Browser/playwright/global-setup.ts',
            'tests/Browser/playwright/helpers/tool-catalog.ts',
            'tests/Browser/playwright/tool-smoke.spec.ts',
            'docs/quality/E2E-LOT-5-UNIVERSAL-SMOKE.md',
        ] as $path) {
            self::assertFileExists(base_path($path), $path);
        }

        $playwright = (string) file_get_contents(base_path('playwright.config.ts'));
        self::assertStringContainsString("globalSetup: './tests/Browser/playwright/global-setup.ts'", $playwright);

        $smoke = (string) file_get_contents(base_path('tests/Browser/playwright/tool-smoke.spec.ts'));
        self::assertStringContainsString('loadToolCatalog()', $smoke);
        self::assertStringContainsString('for (const tool of catalog.tools)', $smoke);
        self::assertStringNotContainsString("'/ferramentas/custo-funcionario-clt'", $smoke);

        $composer = json_decode((string) file_get_contents(base_path('composer.json')), true, flags: JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('e2e:catalog:check', $composer['scripts'] ?? []);
        self::assertArrayHasKey('e2e:browser:smoke', $composer['scripts'] ?? []);
    }

    public function test_official_catalog_and_e2e_inventory_are_synchronized(): void
    {
        $product = require base_path('config/product_tools.php');
        $quality = require base_path('config/e2e_quality.php');

        $official = collect($product['official'] ?? [])->keyBy('slug');
        $inventory = collect($quality['tools'] ?? [])->keyBy('slug');

        self::assertCount(32, $official);
        self::assertSame($official->keys()->sort()->values()->all(), $inventory->keys()->sort()->values()->all());

        foreach ($official as $slug => $tool) {
            self::assertSame($tool['module'], $inventory[$slug]['module'] ?? null, $slug);
            self::assertContains('page_load', $inventory[$slug]['required_scenarios'] ?? [], $slug);
            self::assertContains('form', $inventory[$slug]['surfaces'] ?? [], $slug);
        }
    }
}
