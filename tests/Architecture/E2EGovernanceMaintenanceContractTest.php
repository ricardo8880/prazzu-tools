<?php

declare(strict_types=1);

namespace Tests\Architecture;

use Tests\TestCase;

final class E2EGovernanceMaintenanceContractTest extends TestCase
{
    public function test_lote_12_governance_contract_is_present(): void
    {
        $root = base_path();
        foreach ([
            'config/e2e_governance.php',
            'scripts/e2e-governance.php',
            'tests/Browser/playwright/tool-responsive.spec.ts',
            'tests/Browser/playwright/tool-exploratory.spec.ts',
            'docs/quality/E2E-LOT-12-GOVERNANCE-MAINTENANCE.md',
        ] as $path) {
            self::assertFileExists($root.'/'.$path);
        }

        $config = require $root.'/config/e2e_governance.php';
        self::assertSame(32, $config['catalog']['expected_tool_count']);
        self::assertFalse($config['exploration']['blocking']);
        self::assertContains('firefox-desktop', $config['complete_projects']);
        self::assertContains('webkit-desktop', $config['complete_projects']);
        self::assertGreaterThan(0, $config['retention']['artifacts_days']);
    }
}
