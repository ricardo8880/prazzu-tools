<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\PresumedProfitIrpjCsllCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_is_beta_with_the_expected_identity_and_tiers(): void
    {
        $manifest = (new Tool)->manifest();

        self::assertSame('calculadora-irpj-csll-lucro-presumido', $manifest->slug);
        self::assertSame('tools.calculadora-irpj-csll-lucro-presumido.index', $manifest->routeName);
        self::assertSame(ToolStatus::Beta, $manifest->status);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
        self::assertSame(['pdf', 'xlsx'], $manifest->export?->formats);
    }
}
