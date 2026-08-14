<?php

declare(strict_types=1);

namespace App\Tools\SefazFiscalValidator\Tests\Unit;

use App\Tools\SefazFiscalValidator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_is_valid(): void
    {
        self::assertNotSame('', (new Tool)->manifest()->slug);
    }
}
