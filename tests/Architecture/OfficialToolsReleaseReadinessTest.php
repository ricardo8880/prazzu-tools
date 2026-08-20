<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class OfficialToolsReleaseReadinessTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $inventory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inventory = require dirname(__DIR__, 2).'/config/product_tools.php';
    }

    public function test_all_current_tools_are_in_the_governed_inventory(): void
    {
        self::assertSame('3.19.0', $this->inventory['schema_version']);
        self::assertSame('tool_maturity_lot_1_audited', $this->inventory['release_readiness']);
        self::assertCount(50, $this->inventory['official']);
        self::assertSame(['implemented'], array_values(array_unique(array_column($this->inventory['official'], 'state'))));
        self::assertCount(50, array_unique(array_column($this->inventory['official'], 'module')));
        self::assertCount(50, array_unique(array_column($this->inventory['official'], 'slug')));
    }

    public function test_official_modules_exist_and_are_registered(): void
    {
        $root = dirname(__DIR__, 2);
        $registeredModules = array_merge(...array_values(require $root.'/config/tools/modules.php'));

        foreach ($this->inventory['official'] as $tool) {
            $module = $tool['module'];
            self::assertDirectoryExists($root.'/app/Tools/'.$module);
            self::assertFileExists($root.'/app/Tools/'.$module.'/Tool.php');
            self::assertContains(sprintf('App\\Tools\\%s\\Tool', $module), $registeredModules);
        }
    }

    public function test_no_tool_is_hidden_in_a_parallel_inventory(): void
    {
        self::assertArrayNotHasKey('additional_modules', $this->inventory);
        self::assertSame(50, $this->inventory['expected_module_count']);
    }

    public function test_surgical_lot_documentation_exists(): void
    {
        $root = dirname(__DIR__, 2);

        self::assertFileExists($root.'/docs/SURGICAL-LOT-1-INVENTORY-GOVERNANCE.md');
        self::assertFileExists($root.'/docs/SURGICAL-LOT-2-HOME-LATEST-EIGHT.md');
        self::assertFileExists($root.'/docs/SURGICAL-LOT-3-PRO-LABORE-COMPATIBILITY-BRIDGE.md');
        self::assertFileExists($root.'/docs/SURGICAL-LOT-4-CATALOG-SANITIZATION.md');
        self::assertFileExists($root.'/docs/PRODUCT-TOOLS-INVENTORY.md');
        self::assertFileExists($root.'/docs/RELEASE-CHECKLIST.md');
        self::assertFileExists($root.'/docs/PLUS-REMEDIATION-LOT-8-FINAL-AUDIT.md');
        self::assertFileExists($root.'/scripts/verify-distribution.php');
    }
}
