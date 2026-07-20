<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\TaxRegimeComparator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_active_manifest_has_the_expected_identity(): void
    {
        $manifest = (new Tool())->manifest();

        self::assertSame('comparador-tributario', $manifest->slug);
        self::assertSame('tools.comparador-tributario.index', $manifest->routeName);
        self::assertSame(ToolStatus::Active, $manifest->status);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
