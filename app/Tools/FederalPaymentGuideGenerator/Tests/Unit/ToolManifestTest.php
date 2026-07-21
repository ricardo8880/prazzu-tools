<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Tests\Unit;

use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\FederalPaymentGuideGenerator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_declares_versioned_history_and_professional_exports(): void
    {
        $tool = new Tool();
        $manifest = $tool->manifest();

        self::assertSame('1.1.0', $manifest->version);
        self::assertSame(ToolStatus::Active, $manifest->status);
        self::assertTrue($manifest->supportsHistory);
        self::assertTrue($manifest->persistence->enabled);
        self::assertContains(ToolCapability::History, $manifest->capabilities);
        self::assertContains(ToolCapability::VersionedPersistence, $manifest->capabilities);
        self::assertContains(ToolCapability::Export, $manifest->capabilities);
        self::assertTrue($manifest->export->enabled);
        self::assertSame(['csv', 'json', 'pdf'], $manifest->export->formats);
        self::assertTrue($tool->historyPolicy()->enabled);
    }
}
