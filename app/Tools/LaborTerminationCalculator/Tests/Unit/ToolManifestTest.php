<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Tests\Unit;

use App\Tools\LaborTerminationCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_is_us_final_pay_tool(): void
    {
        $manifest = (new Tool)->manifest();
        self::assertSame('final-pay-severance-calculator', $manifest->slug);
        self::assertSame('Final Pay & Severance Calculator', $manifest->name);
    }
}
