<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\ReceiptIssuer\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_active_manifest_has_the_expected_identity(): void
    {
        $manifest = (new Tool())->manifest();

        self::assertSame('emissor-de-recibos', $manifest->slug);
        self::assertSame('tools.emissor-de-recibos.index', $manifest->routeName);
        self::assertSame(ToolStatus::Active, $manifest->status);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
