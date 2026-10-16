<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Tests\Unit; use App\Tools\TaxReformSimulator\Tool; use PHPUnit\Framework\TestCase; final class ToolManifestTest extends TestCase { public function test_manifest():void{self::assertSame('sales-tax-calculator',(new Tool)->manifest()->slug);} }
