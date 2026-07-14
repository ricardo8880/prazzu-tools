<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\LaborTerminationCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_exposes_the_labor_termination_tool(): void
    {
        $manifest = (new Tool)->manifest();

        self::assertSame('calculadora-de-rescisao', $manifest->slug);
        self::assertSame('Calculadora de Rescisão Trabalhista', $manifest->name);
        self::assertSame(ToolCategory::Labor, $manifest->category);
        self::assertSame(ToolStatus::Active, $manifest->status);
        self::assertSame('1.0.0', $manifest->version);
        self::assertSame('tools.calculadora-de-rescisao.index', $manifest->routeName);
        self::assertTrue($manifest->supportsHistory);
        self::assertTrue($manifest->storesSensitiveData);
        self::assertSame(180, (new Tool)->historyPolicy()->retentionDays);
    }
}
