<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Tests\Unit;

use App\Tools\NetSalaryCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolIntegrationContractTest extends TestCase
{
    public function test_tool_does_not_declare_artificial_integrations(): void
    {
        $integrations = (new Tool)->integrations();

        self::assertSame([], $integrations->publishes);
        self::assertSame([], $integrations->accepts);
    }
}
