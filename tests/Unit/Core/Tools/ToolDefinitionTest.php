<?php

namespace Tests\Unit\Core\Tools;

use App\Core\Tools\Data\ToolDefinition;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToolDefinitionTest extends TestCase
{
    public function test_tool_cannot_be_free_and_premium_at_the_same_time(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ToolDefinition(
            slug: 'example',
            name: 'Example',
            description: 'Example tool',
            category: 'calculadoras',
            icon: 'bi-calculator',
            routeName: 'tools.example.index',
            isFree: true,
            isPremium: true,
        );
    }
}
