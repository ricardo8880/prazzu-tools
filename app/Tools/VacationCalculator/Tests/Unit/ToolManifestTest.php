<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\VacationCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_is_active_with_the_expected_identity(): void
    {
        $manifest = (new Tool())->manifest();

        self::assertSame('calculadora-ferias', $manifest->slug);
        self::assertSame('Calculadora de Férias', $manifest->name);
        self::assertSame('tools.calculadora-ferias.index', $manifest->routeName);
        self::assertSame(ToolStatus::Active, $manifest->status);
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
