<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class ProductSatisfactionFinalConsolidationTest extends TestCase
{
    public function test_all_eight_product_satisfaction_lot_reports_are_present(): void
    {
        $root = dirname(__DIR__, 2);

        foreach ([
            'PRODUCT-SATISFACTION-LOT-1-TRUST.md',
            'PRODUCT-SATISFACTION-LOT-2-PROGRESSIVE-DISCLOSURE.md',
            'PRODUCT-SATISFACTION-LOT-3-RESULT-INTERPRETATION.md',
            'PRODUCT-SATISFACTION-LOT-4-RESOLUTION-FEEDBACK.md',
            'PRODUCT-SATISFACTION-LOT-5-DISCOVERY-JOURNEYS.md',
            'PRODUCT-SATISFACTION-LOT-6-CONTINUITY-CONTEXT.md',
            'PRODUCT-SATISFACTION-LOT-7-ACCOUNT-FAVORITES-RETURN.md',
            'PRODUCT-SATISFACTION-LOT-8-FINAL-CONSOLIDATION.md',
        ] as $report) {
            self::assertFileExists($root.'/docs/'.$report);
        }
    }

    public function test_cross_lot_experience_remains_composed_from_shared_surfaces(): void
    {
        $root = dirname(__DIR__, 2);
        $toolPage = file_get_contents($root.'/resources/views/components/tools/page.blade.php');

        self::assertIsString($toolPage);
        self::assertStringContainsString('<x-feedback.tool-resolution', $toolPage);
        self::assertStringContainsString('account.tools.favorite', $toolPage);
        self::assertStringContainsString("source' => 'tool_favorite", $toolPage);

        foreach ([
            'resources/views/components/tools/normative-trust.blade.php',
            'resources/views/components/tools/form-disclosure.blade.php',
            'resources/views/components/tools/result-insight.blade.php',
            'resources/views/components/tools/problem-journeys.blade.php',
        ] as $sharedSurface) {
            self::assertFileExists($root.'/'.$sharedSurface);
        }
    }

    public function test_inventory_and_product_boundaries_remain_unchanged_by_the_experience_cycle(): void
    {
        $root = dirname(__DIR__, 2);
        $inventory = require $root.'/config/product_tools.php';
        $readme = file_get_contents($root.'/README.md');

        self::assertCount(50, $inventory['official']);
        self::assertSame(50, $inventory['expected_module_count']);
        self::assertCount(50, array_unique(array_column($inventory['official'], 'slug')));
        self::assertIsString($readme);
        self::assertStringContainsString('Acesso às ferramentas não depende de conta.', $readme);
        self::assertStringContainsString('Persistência,', $readme);
        self::assertStringContainsString('sincronização e histórico dependem de conta.', $readme);
    }

    public function test_final_distribution_gate_is_part_of_the_quality_pipeline(): void
    {
        $root = dirname(__DIR__, 2);
        $workflow = file_get_contents($root.'/.github/workflows/quality.yml');
        $packager = file_get_contents($root.'/scripts/package-distribution.ps1');
        $verifier = file_get_contents($root.'/scripts/verify-distribution.php');

        self::assertIsString($workflow);
        self::assertIsString($packager);
        self::assertIsString($verifier);
        self::assertStringContainsString('package-distribution.ps1', $workflow);
        self::assertStringContainsString('verify-distribution.php', $packager);
        self::assertStringContainsString('sqlite', $verifier);
        self::assertStringContainsString('sql', $verifier);
    }
}
