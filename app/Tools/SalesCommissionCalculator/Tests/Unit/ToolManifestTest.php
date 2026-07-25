<?php

declare(strict_types=1);

namespace App\Tools\SalesCommissionCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\SalesCommissionCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_is_publicly_visible_in_beta_with_the_expected_identity(): void
    {
        $manifest = (new Tool)->manifest();

        self::assertSame('comissao-vendedores', $manifest->slug);
        self::assertSame('tools.comissao-vendedores.index', $manifest->routeName);
        self::assertSame(ToolStatus::Beta, $manifest->status);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
