<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Unit;

use App\Tools\VacationCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_describes_us_pto_calculator(): void
    {
        $manifest = (new Tool)->manifest();

        self::assertSame('pto-calculator', $manifest->slug);
        self::assertSame('PTO Calculator', $manifest->name);
        self::assertSame('tools.pto-calculator.index', $manifest->routeName);
    }
}
