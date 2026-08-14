<?php

declare(strict_types=1);

namespace App\Tools\ActualProfitCalculator\Tests\Unit; use App\Tools\ActualProfitCalculator\Tool; use PHPUnit\Framework\TestCase; final class ToolManifestTest extends TestCase { public function test_manifest():void{self::assertSame('calculadora-lucro-real',(new Tool)->manifest()->slug);} }
