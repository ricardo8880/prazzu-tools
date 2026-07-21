<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\ProLaboreProfitDistributionCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_release_candidate_manifest_is_complete(): void
    {
        $manifest = (new Tool)->manifest();
        self::assertSame('1.0.0-rc.1', $manifest->version);
        self::assertSame(ToolStatus::Beta, $manifest->status);
        self::assertTrue($manifest->supportsHistory);
        self::assertTrue($manifest->hasCapability(ToolCapability::History));
        self::assertTrue($manifest->hasCapability(ToolCapability::VersionedPersistence));
        self::assertTrue($manifest->hasCapability(ToolCapability::Export));
        self::assertTrue($manifest->persistence?->enabled);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
