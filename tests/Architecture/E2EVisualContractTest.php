<?php

namespace Tests\Architecture;

use Tests\TestCase;

final class E2EVisualContractTest extends TestCase
{
    public function test_shared_tool_components_expose_stable_test_ids(): void
    {
        $contracts = [
            'resources/views/components/tools/page.blade.php' => ["TestId::make('tool-page', $slug)", 'data-tool="{{ $slug }}"'],
            'resources/views/components/tools/form-panel.blade.php' => ["'testId' => 'tool-form-panel'", "'data-testid' => $testId"],
            'resources/views/components/tools/result-panel.blade.php' => ["'testId' => 'tool-result'", "'data-testid' => $testId"],
            'resources/views/components/tools/validation-summary.blade.php' => ["'data-testid' => 'validation-summary'"],
            'resources/views/components/tools/export-buttons.blade.php' => ['data-testid="download-actions"', 'data-testid="download-pdf"', 'data-testid="download-xlsx"'],
            'resources/views/components/tools/form/input.blade.php' => ['TestId::field($name)'],
            'resources/views/components/tools/form/select.blade.php' => ['TestId::field($name)'],
            'resources/views/components/tools/form/switch.blade.php' => ['TestId::field($name)'],
        ];

        foreach ($contracts as $path => $needles) {
            $contents = (string) file_get_contents(base_path($path));
            foreach ($needles as $needle) {
                self::assertStringContainsString($needle, $contents, "Contrato visual ausente em {$path}: {$needle}");
            }
        }
    }

    public function test_playwright_pilot_consumes_only_the_stable_contract(): void
    {
        $contents = (string) file_get_contents(base_path('tests/Browser/playwright/foundation.spec.ts'));

        self::assertStringContainsString("getByTestId('tool-page-custo-funcionario-clt')", $contents);
        self::assertStringContainsString("getByTestId('tool-form-panel')", $contents);
        self::assertStringNotContainsString("locator('form').first()", $contents);
        self::assertStringNotContainsString('getByRole(\'button\').first()', $contents);
    }
}
