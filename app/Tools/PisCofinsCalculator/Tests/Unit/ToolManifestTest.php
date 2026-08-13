<?php

declare(strict_types=1);

namespace App\Tools\PisCofinsCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\PisCofinsCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_has_the_expected_identity_and_tiers(): void
    {
        $manifest = (new Tool)->manifest();

        self::assertSame('calculadora-pis-cofins', $manifest->slug);
        self::assertSame('tools.calculadora-pis-cofins.index', $manifest->routeName);
        self::assertSame(ToolStatus::Beta, $manifest->status);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
