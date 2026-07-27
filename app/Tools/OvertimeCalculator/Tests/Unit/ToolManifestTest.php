<?php

declare(strict_types=1);
namespace App\Tools\OvertimeCalculator\Tests\Unit;
use App\Core\Tools\Enums\ToolFeatureTier; use App\Tools\OvertimeCalculator\Tool; use PHPUnit\Framework\TestCase;
final class ToolManifestTest extends TestCase { public function test_manifest_has_essential_and_plus(): void { $m=(new Tool)->manifest(); self::assertSame('calculadora-hora-extra',$m->slug); self::assertNotEmpty($m->featuresFor(ToolFeatureTier::Essential)); self::assertNotEmpty($m->featuresFor(ToolFeatureTier::Plus)); } }
