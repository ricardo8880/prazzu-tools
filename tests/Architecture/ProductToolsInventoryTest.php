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
        $this->inventory = require dirname(__DIR__, 2).'/config/product_tools.php';
    }

    public function test_official_inventory_has_the_current_ordered_unique_tools(): void
    {
        $tools = $this->inventory['official'];

        self::assertSame(43, $this->inventory['expected_module_count']);
        self::assertCount(43, $tools);
        self::assertSame(range(1, 43), array_column($tools, 'id'));

        foreach (['key', 'name', 'slug', 'module'] as $field) {
            self::assertCount(43, array_unique(array_column($tools, $field)), "O campo [{$field}] deve ser único.");
        }
    }

    public function test_release_order_is_complete_unique_and_independent_from_editorial_position(): void
    {
        $tools = $this->inventory['official'];
        $releaseOrders = array_column($tools, 'release_order');

        self::assertCount(43, array_unique($releaseOrders));
        sort($releaseOrders);
        self::assertSame(range(1, 43), $releaseOrders);

        usort($tools, static fn (array $left, array $right): int => $right['release_order'] <=> $left['release_order']);
        $latest = array_column(array_slice($tools, 0, 8), 'slug');

        self::assertSame([
            'calculadora-das-retroativo-regularizacao-simples',
            'simulador-distribuicao-lucros-balanco',
            'calculadora-iss',
            'simulador-mei-microempresa',
            'calculadora-parcelamento-tributario',
            'calculadora-depreciacao-ativos',
            'calculadora-retencoes-nota-fiscal',
            'calculadora-icms-st',
        ], $latest);
    }

    public function test_every_current_module_is_official_and_classified_once(): void
    {
        $root = dirname(__DIR__, 2);
        $actualModules = array_map('basename', glob($root.'/app/Tools/*', GLOB_ONLYDIR) ?: []);
        sort($actualModules);

        $officialModules = array_column($this->inventory['official'], 'module');
        sort($officialModules);

        self::assertSame($actualModules, $officialModules);
        self::assertCount(43, array_unique($officialModules));
        self::assertArrayNotHasKey('additional_modules', $this->inventory);
    }

    public function test_partner_withdrawal_planner_has_a_distinct_resolved_scope(): void
    {
        $reviews = array_values(array_filter(
            $this->inventory['functional_overlap_reviews'],
            static fn (array $review): bool => $review['module'] === 'ProLaboreProfitDistributionCalculator',
        ));

        self::assertCount(1, $reviews);
        self::assertSame('resolved_distinct_planning_scope', $reviews[0]['classification']);
        self::assertSame('resolved', $reviews[0]['state']);
        self::assertSame(['ProLaboreSimulator', 'ProfitDistributionCalculator'], $reviews[0]['related_modules']);

        $official = array_column($this->inventory['official'], null, 'module');
        self::assertSame('implemented', $official['ProLaboreProfitDistributionCalculator']['state']);
        self::assertSame('Planejador de Retirada de Sócios', $official['ProLaboreProfitDistributionCalculator']['name']);
    }

    public function test_governance_documents_exist(): void
    {
        $root = dirname(__DIR__, 2);

        self::assertFileExists($root.'/'.$this->inventory['source']);
        self::assertFileExists($root.'/'.$this->inventory['continuity_log']);
        self::assertFileExists($root.'/'.$this->inventory['inventory_document']);
        self::assertFileExists($root.'/CORE_CANDIDATES.md');
        self::assertFileExists($root.'/docs/SURGICAL-LOT-1-INVENTORY-GOVERNANCE.md');
    }
}
