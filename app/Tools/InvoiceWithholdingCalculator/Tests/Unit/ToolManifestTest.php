<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\InvoiceWithholdingCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_has_expected_identity_and_tiers(): void
    {
        $m = (new Tool)->manifest();
        self::assertSame('calculadora-retencoes-nota-fiscal', $m->slug);
        self::assertSame('tools.calculadora-retencoes-nota-fiscal.index', $m->routeName);
        self::assertSame(ToolStatus::Beta, $m->status);
        self::assertNotEmpty($m->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($m->featuresFor(ToolFeatureTier::Plus));
    }
}
