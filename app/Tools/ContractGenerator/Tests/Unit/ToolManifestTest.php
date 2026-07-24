<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Unit;

use App\Core\Tools\Enums\ToolCapability;
use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\ContractGenerator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_declares_contract_generator_capabilities(): void
    {
        $manifest = (new Tool())->manifest();

        self::assertSame('gerador-de-contratos', $manifest->slug);
        self::assertSame(ToolCategory::Generators, $manifest->category);
        self::assertSame(ToolStatus::Beta, $manifest->status);
        self::assertSame('0.5.0', $manifest->version);
        self::assertCount(4, $manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertCount(6, $manifest->featuresFor(ToolFeatureTier::Plus));
        self::assertTrue($manifest->supportsHistory);
        self::assertTrue($manifest->storesSensitiveData);
        self::assertSame([
            ToolCapability::History,
            ToolCapability::VersionedPersistence,
            ToolCapability::Export,
            ToolCapability::SensitiveData,
        ], $manifest->capabilities);
        self::assertTrue($manifest->export?->enabled ?? false);
        self::assertSame(['pdf', 'docx', 'json'], $manifest->export?->formats);
        self::assertTrue($manifest->persistence?->enabled ?? false);
        self::assertSame(1, $manifest->persistence?->schemaVersion);
        self::assertFalse($manifest->sharing?->enabled ?? true);
        self::assertNotNull($manifest->sensitiveData);
    }
}
