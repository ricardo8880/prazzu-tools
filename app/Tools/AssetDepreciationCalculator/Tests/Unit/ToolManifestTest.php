<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\AssetDepreciationCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_has_expected_identity_and_tiers(): void
    {
        $manifest = (new Tool())->manifest();
        self::assertSame('calculadora-depreciacao-ativos', $manifest->slug);
        self::assertSame('tools.calculadora-depreciacao-ativos.index', $manifest->routeName);
        self::assertSame(ToolStatus::Beta, $manifest->status);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
