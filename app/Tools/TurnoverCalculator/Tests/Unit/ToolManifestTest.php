<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Tests\Unit;

use App\Tools\TurnoverCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_declares_rh_vertical(): void
    {
        self::assertSame('rh', (new Tool)->manifest()->vertical);
        self::assertSame('employee-turnover-calculator', (new Tool)->manifest()->slug);
    }
}
