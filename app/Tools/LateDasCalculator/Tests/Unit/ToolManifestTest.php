<?php

declare(strict_types=1);

namespace App\Tools\LateDasCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\LateDasCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_is_publicly_visible_in_beta_with_the_expected_identity(): void
    {
        $manifest = (new Tool)->manifest();

        self::assertSame('das-em-atraso', $manifest->slug);
        self::assertSame('tools.das-em-atraso.index', $manifest->routeName);
        self::assertSame(ToolStatus::Beta, $manifest->status);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
