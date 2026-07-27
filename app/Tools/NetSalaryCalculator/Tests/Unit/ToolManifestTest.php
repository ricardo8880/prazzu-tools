<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\NetSalaryCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_exposes_complete_essential_and_plus_capabilities(): void
    {
        $manifest = (new Tool)->manifest();

        self::assertSame('calculadora-salario-liquido', $manifest->slug);
        self::assertSame('tools.calculadora-salario-liquido.index', $manifest->routeName);
        self::assertSame(ToolStatus::Beta, $manifest->status);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
