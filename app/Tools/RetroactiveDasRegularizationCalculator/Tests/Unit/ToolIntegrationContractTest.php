<?php

declare(strict_types=1);

namespace App\Tools\RetroactiveDasRegularizationCalculator\Tests\Unit;

use App\Tools\RetroactiveDasRegularizationCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolIntegrationContractTest extends TestCase
{
    public function test_tool_does_not_publish_or_accept_cross_tool_contracts(): void
    {
        $i = (new Tool)->integrations();
        self::assertSame([], $i->publishes);
        self::assertSame([], $i->accepts);
    }
}
