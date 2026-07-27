<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class ProductToolsInventoryTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var array<string, mixed> $inventory */
        $inventory = require dirname(__DIR__, 2).'/config/product_tools.php';
        $this->inventory = $inventory;
    }

    public function test_official_inventory_has_exactly_twenty_three_ordered_unique_tools(): void
    {
        $tools = $this->inventory['official'];

        self::assertCount(23, $tools);
        self::assertSame(range(1, 23), array_column($tools, 'id'));
        self::assertCount(23, array_unique(array_column($tools, 'key')));
        self::assertCount(23, array_unique(array_column($tools, 'name')));
    }

    public function test_expansion_tools_are_promoted_to_the_official_catalog(): void
    {
        $byKey = [];
        foreach ($this->inventory['official'] as $tool) {
            $byKey[$tool['key']] = $tool;
        }

        self::assertSame('NetSalaryCalculator', $byKey['net-salary']['module']);
        self::assertSame('calculadora-salario-liquido', $byKey['net-salary']['slug']);
        self::assertSame('OvertimeCalculator', $byKey['overtime']['module']);
        self::assertSame('calculadora-hora-extra', $byKey['overtime']['slug']);
        self::assertSame('DifalIcmsCalculator', $byKey['difal-icms']['module']);
        self::assertSame('calculadora-difal-icms', $byKey['difal-icms']['slug']);

        $additional = array_column($this->inventory['additional_modules'], 'module');
        self::assertNotContains('NetSalaryCalculator', $additional);
        self::assertNotContains('OvertimeCalculator', $additional);
        self::assertNotContains('DifalIcmsCalculator', $additional);
    }

    public function test_every_current_module_is_classified_once(): void
    {
        $root = dirname(__DIR__, 2);
        $actualModules = array_values(array_filter(
            array_map('basename', glob($root.'/app/Tools/*', GLOB_ONLYDIR) ?: []),
            static fn (string $module): bool => $module !== '',
        ));
        sort($actualModules);

        $officialModules = array_values(array_unique(array_column($this->inventory['official'], 'module')));
        $additionalModules = array_column($this->inventory['additional_modules'], 'module');
        $classifiedModules = array_merge($officialModules, $additionalModules);
        sort($classifiedModules);

        self::assertSame($actualModules, $classifiedModules);
        self::assertCount(count($classifiedModules), array_unique($classifiedModules));
    }

    public function test_pro_labore_and_profit_distribution_are_independent_modules(): void
    {
        $moduleCounts = array_count_values(array_column($this->inventory['official'], 'module'));
        $duplicates = array_filter($moduleCounts, static fn (int $count): bool => $count > 1);

        self::assertSame([], $duplicates);

        $byKey = [];
        foreach ($this->inventory['official'] as $tool) {
            $byKey[$tool['key']] = $tool;
        }

        self::assertSame('ProLaboreSimulator', $byKey['pro-labore']['module']);
        self::assertSame('ProfitDistributionCalculator', $byKey['profit-distribution']['module']);
        self::assertSame('implemented', $byKey['pro-labore']['state']);
        self::assertSame('implemented', $byKey['profit-distribution']['state']);
    }

    public function test_governance_documents_exist(): void
    {
        $root = dirname(__DIR__, 2);

        self::assertFileExists($root.'/'.$this->inventory['source']);
        self::assertFileExists($root.'/'.$this->inventory['continuity_log']);
        self::assertFileExists($root.'/docs/PRODUCT-TOOLS-INVENTORY.md');
        self::assertFileExists($root.'/CORE_CANDIDATES.md');
    }
}
