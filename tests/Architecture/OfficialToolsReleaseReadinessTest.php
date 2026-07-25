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

    public function test_all_official_tools_are_release_aligned(): void
    {
        self::assertSame('2.0.0', $this->inventory['schema_version']);
        self::assertSame('lot_10_audited', $this->inventory['release_readiness']);
        self::assertCount(20, $this->inventory['official']);
        self::assertSame(['implemented'], array_values(array_unique(array_column($this->inventory['official'], 'state'))));
        self::assertCount(20, array_unique(array_column($this->inventory['official'], 'module')));
        self::assertCount(20, array_unique(array_column($this->inventory['official'], 'slug')));
    }

    public function test_official_modules_exist_and_are_registered(): void
    {
        $root = dirname(__DIR__, 2);
        $moduleConfig = file_get_contents($root.'/config/tools/modules.php');
        self::assertNotFalse($moduleConfig);

        foreach ($this->inventory['official'] as $tool) {
            $module = $tool['module'];
            self::assertDirectoryExists($root.'/app/Tools/'.$module);
            self::assertFileExists($root.'/app/Tools/'.$module.'/Tool.php');
            self::assertStringContainsString('App\\Tools\\'.$module.'\\Tool::class', $moduleConfig);
        }
    }

    public function test_legacy_combined_module_requires_migration_audit_before_removal(): void
    {
        $legacy = array_values(array_filter(
            $this->inventory['additional_modules'],
            static fn (array $module): bool => $module['module'] === 'ProLaboreProfitDistributionCalculator',
        ));

        self::assertCount(1, $legacy);
        self::assertSame('legacy_compatibility', $legacy[0]['classification']);
        self::assertSame('preserve_until_migration_audit', $legacy[0]['catalog_visibility']);
        self::assertSame(['ProLaboreSimulator', 'ProfitDistributionCalculator'], $legacy[0]['replacement_modules']);
    }

    public function test_release_audit_documentation_exists(): void
    {
        $root = dirname(__DIR__, 2);

        self::assertFileExists($root.'/docs/LOT-10-RELEASE-AUDIT.md');
        self::assertFileExists($root.'/docs/RELEASE-CHECKLIST.md');
        self::assertFileExists($root.'/scripts/verify-distribution.php');
    }
}
