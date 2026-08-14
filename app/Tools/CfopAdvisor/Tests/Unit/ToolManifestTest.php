<?php

declare(strict_types=1);

namespace App\Tools\CfopAdvisor\Tests\Unit;

use App\Tools\CfopAdvisor\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_is_valid(): void
    {
        self::assertNotSame('', (new Tool)->manifest()->slug);
    }
}
