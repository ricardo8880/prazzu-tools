<?php

declare(strict_types=1);

namespace App\Tools\IcmsCalculator\Tests\Unit;

use App\Tools\IcmsCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_is_valid(): void
    {
        self::assertNotSame('', (new Tool)->manifest()->slug);
    }
}
