<?php

declare(strict_types=1);

namespace Tests\Unit\Tools\Analytics;

use App\Core\Tools\Analytics\Data\ToolAnalyticsField;
use App\Core\Tools\Analytics\Data\ToolAnalyticsForm;
use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToolAnalyticsFrontendContractTest extends TestCase
{
    public function test_it_serializes_only_the_declared_frontend_contract(): void
    {
        $journey = new ToolAnalyticsJourney(
            toolSlug: 'calculadora-exemplo',
            forms: [
                new ToolAnalyticsForm(
                    key: 'main',
                    steps: ['input', 'review'],
                    fields: [
                        new ToolAnalyticsField('revenue', 'input', true, '[name="revenue"]'),
                    ],
                    actions: ['calculate', 'export'],
                    selector: '#calculator-form',
                    resultSelector: '#calculation-result',
                ),
            ],
        );

        self::assertSame([
            'tool' => 'calculadora-exemplo',
            'schema_version' => 1,
            'forms' => [[
                'key' => 'main',
                'steps' => ['input', 'review'],
                'fields' => [[
                    'key' => 'revenue',
                    'step' => 'input',
                    'required' => true,
                    'selector' => '[name="revenue"]',
                ]],
                'actions' => ['calculate', 'export'],
                'selector' => '#calculator-form',
                'result_selector' => '#calculation-result',
            ]],
        ], $journey->toFrontendArray());
    }

    public function test_it_rejects_unsafe_or_unbounded_selectors(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ToolAnalyticsField('revenue', 'input', selector: "input\nscript");
    }

    public function test_it_rejects_duplicate_actions(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ToolAnalyticsForm(
            key: 'main',
            steps: ['input'],
            fields: [],
            actions: ['calculate', 'calculate'],
        );
    }
}
