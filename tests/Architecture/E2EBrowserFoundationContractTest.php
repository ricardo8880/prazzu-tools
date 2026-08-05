<?php

namespace Tests\Architecture;

use Tests\TestCase;

final class E2EBrowserFoundationContractTest extends TestCase
{
    public function test_browser_foundation_files_and_scripts_are_registered(): void
    {
        foreach ([
            'playwright.config.ts',
            'tests/Browser/playwright/foundation.spec.ts',
            'tests/Browser/playwright/helpers/diagnostics.ts',
            'scripts/e2e-browser.php',
            'docs/quality/E2E-LOT-3-PLAYWRIGHT-FOUNDATION.md',
        ] as $path) {
            self::assertFileExists(base_path($path), $path);
        }

        $package = json_decode((string) file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('1.62.1', $package['devDependencies']['@playwright/test'] ?? null);
        self::assertSame('playwright test', $package['scripts']['e2e:test'] ?? null);

        $composer = json_decode((string) file_get_contents(base_path('composer.json')), true, flags: JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('e2e:browser:test', $composer['scripts'] ?? []);
    }
}
