<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Architecture;

use App\Core\Tools\Support\ToolModuleStructure;
use App\Core\Tools\Support\ToolModuleValidator;
use App\Tools\ContractGenerator\Tool;
use PHPUnit\Framework\TestCase;

final class ModuleArchitectureTest extends TestCase
{
    public function test_module_matches_final_architecture(): void
    {
        self::assertSame([], ToolModuleStructure::missingPaths(dirname(__DIR__, 2)));
        (new ToolModuleValidator)->validate(new Tool);
        self::addToAssertionCount(1);
    }
}
