<?php

declare(strict_types=1);

namespace App\Tools\DigitalCertificateAnalyzer\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\DigitalCertificateAnalyzer\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_has_final_lot_two_contract(): void
    {
        $manifest = (new Tool)->manifest();
        self::assertSame('analisador-certificado-digital-a1', $manifest->slug);
        self::assertSame('Analisador de Certificado Digital A1', $manifest->name);
        self::assertSame(ToolStatus::Active, $manifest->status);
        self::assertFalse($manifest->supportsHistory);
        self::assertTrue($manifest->storesSensitiveData);
        self::assertSame(['pdf'], $manifest->export?->formats);
        self::assertCount(2, $manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertSame('technical_report', $manifest->featuresFor(ToolFeatureTier::Plus)[0]->key);
    }
}
