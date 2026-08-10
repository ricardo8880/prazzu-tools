<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class MultiVerticalExpansionProofTest extends TestCase
{
    public function test_second_vertical_is_data_and_not_parallel_infrastructure(): void
    {
        $verticals = require dirname(__DIR__, 2).'/config/verticals.php';
        $inventory = require dirname(__DIR__, 2).'/config/product_tools.php';

        self::assertSame('Recursos Humanos', $verticals['registered']['rh']['name']);
        self::assertSame(37, $inventory['expected_module_count']);
        self::assertCount(36, array_filter($inventory['official'], static fn (array $tool): bool => $tool['vertical'] === 'contabilidade'));
        self::assertCount(1, array_filter($inventory['official'], static fn (array $tool): bool => $tool['vertical'] === 'rh'));

        $root = dirname(__DIR__, 2).'/app';
        self::assertDirectoryDoesNotExist($root.'/RH');
        self::assertFileDoesNotExist($root.'/Http/Controllers/HomeRHController.php');
        self::assertFileDoesNotExist($root.'/Core/Analytics/RHAnalytics.php');
    }
}
