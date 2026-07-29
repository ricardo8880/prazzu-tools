<?php

declare(strict_types=1);

namespace Tests\Unit\Tools\Analytics;

use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToolAnalyticsJourneyTest extends TestCase
{
    public function test_a_tool_can_declare_a_safe_shared_journey_contract(): void
    {
        $journey = new ToolAnalyticsJourney(
            toolSlug: 'calculadora-exemplo',
            forms: [new ToolAnalyticsForm(
                key: 'main',
                steps: ['input', 'result'],
                fields: [new ToolAnalyticsField('monthly_revenue', 'input', true)],
                actions: ['calculate', 'export'],
            )],
        );

        self::assertSame('calculadora-exemplo', $journey->toolSlug);
        self::assertSame('monthly_revenue', $journey->forms[0]->fields[0]->key);
    }

    public function test_a_field_cannot_reference_an_unknown_step(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ToolAnalyticsForm(
            key: 'main',
            steps: ['input'],
            fields: [new ToolAnalyticsField('total', 'result')],
        );
    }
}
