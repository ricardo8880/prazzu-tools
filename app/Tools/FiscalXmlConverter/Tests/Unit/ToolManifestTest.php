<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Tests\Unit;

use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\FiscalXmlConverter\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_is_active_and_declares_plus_infrastructure(): void
    {
        $manifest = (new Tool())->manifest();

        self::assertSame('conversor-fiscal-xml', $manifest->slug);
        self::assertSame('1.0.0', $manifest->version);
        self::assertSame(ToolStatus::Active, $manifest->status);
        self::assertTrue($manifest->supportsHistory);
        self::assertTrue($manifest->hasCapability(ToolCapability::History));
        self::assertTrue($manifest->hasCapability(ToolCapability::VersionedPersistence));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
