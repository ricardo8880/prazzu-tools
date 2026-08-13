<?php

declare(strict_types=1);

namespace App\Tools\RetroactiveDasRegularizationCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\RetroactiveDasRegularizationCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_has_expected_identity_and_tiers(): void
    {
        $manifest = (new Tool)->manifest();
        self::assertSame('calculadora-das-retroativo-regularizacao-simples', $manifest->slug);
        self::assertSame('tools.calculadora-das-retroativo-regularizacao-simples.index', $manifest->routeName);
        self::assertSame(ToolStatus::Beta, $manifest->status);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
