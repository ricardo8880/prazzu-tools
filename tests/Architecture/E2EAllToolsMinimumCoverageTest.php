<?php

namespace Tests\Architecture;

use App\Core\Quality\E2E\Data\ToolScenario;
use Tests\TestCase;

final class E2EAllToolsMinimumCoverageTest extends TestCase
{
    public function test_all_official_tools_have_valid_and_invalid_scenarios(): void
    {
        $product = require base_path('config/product_tools.php');
        $config = require base_path('config/e2e_scenarios.php');
        $official = array_column($product['official'] ?? [], 'slug');

        self::assertCount(32, $official);
        $configured = array_keys($config['tools'] ?? []);
        sort($official);
        sort($configured);

        self::assertSame($official, $configured);

        foreach ($official as $slug) {
            $scenarios = $config['tools'][$slug] ?? [];
            self::assertNotEmpty($scenarios, "Ferramenta [{$slug}] sem cenários E2E.");
            self::assertContainsOnlyInstancesOf(ToolScenario::class, $scenarios);
            $kinds = array_map(static fn (ToolScenario $scenario): string => $scenario->kind, $scenarios);
            self::assertContains('valid', $kinds, "Ferramenta [{$slug}] sem cenário válido.");
            self::assertContains('invalid', $kinds, "Ferramenta [{$slug}] sem cenário inválido.");
        }
    }

    public function test_contract_generator_valid_scenario_requires_the_generated_contract(): void
    {
        $config = require base_path('config/e2e_scenarios.php');
        $scenarios = $config['tools']['gerador-de-contratos'] ?? [];
        $valid = collect($scenarios)->first(
            static fn (ToolScenario $scenario): bool => $scenario->kind === 'valid',
        );

        self::assertInstanceOf(ToolScenario::class, $valid);
        self::assertContains(['type' => 'hidden', 'test_id' => 'tool-form-panel'], $valid->expectations);
        self::assertContains(['type' => 'visible', 'test_id' => 'contract-editor'], $valid->expectations);
        self::assertContains(['type' => 'visible', 'test_id' => 'contract-preview'], $valid->expectations);
        self::assertContains(['type' => 'visible', 'test_id' => 'contract-export-pdf'], $valid->expectations);
        self::assertContains(['type' => 'visible', 'test_id' => 'contract-export-xlsx'], $valid->expectations);
        self::assertContains(['type' => 'visible', 'test_id' => 'contract-export-docx'], $valid->expectations);
    }

    public function test_lot_10_runner_enforces_full_minimum_coverage(): void
    {
        $script = (string) file_get_contents(base_path('scripts/e2e-tool-scenarios.php'));
        $helper = (string) file_get_contents(base_path('tests/Browser/playwright/helpers/tool-scenarios.ts'));

        self::assertStringContainsString("minimum_coverage", (string) file_get_contents(base_path('config/e2e_scenarios.php')));
        self::assertStringContainsString('Cobertura incompleta', $script);
        self::assertStringContainsString('auto_fill_form', $helper);
        self::assertStringContainsString('invalidate_required', $helper);
    }
}
