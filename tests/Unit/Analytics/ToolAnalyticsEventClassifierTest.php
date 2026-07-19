<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Services\ToolAnalyticsEventClassifier;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ToolAnalyticsEventClassifierTest extends TestCase
{
    #[DataProvider('classifiedActions')]
    public function test_it_classifies_only_meaningful_tool_actions(
        string $action,
        string $method,
        ?AnalyticsEventName $expected,
    ): void {
        self::assertSame($expected, (new ToolAnalyticsEventClassifier)->classify($action, $method));
    }

    /** @return iterable<string, array{string, string, ?AnalyticsEventName}> */
    public static function classifiedActions(): iterable
    {
        yield 'open tool' => ['index', 'GET', AnalyticsEventName::ToolOpened];
        yield 'calculate' => ['calculate', 'POST', AnalyticsEventName::ToolCalculationCompleted];
        yield 'batch calculation' => ['batch.calculate', 'POST', AnalyticsEventName::ToolCalculationCompleted];
        yield 'validator result' => ['validate', 'POST', AnalyticsEventName::ToolCalculationCompleted];
        yield 'export' => ['history.pdf', 'GET', AnalyticsEventName::ToolResultExported];
        yield 'plus feature' => ['plus.project', 'POST', AnalyticsEventName::ToolPlusUsed];
        yield 'history' => ['history.index', 'GET', AnalyticsEventName::ToolHistoryViewed];
        yield 'crm store is not a calculation' => ['crm.store', 'POST', null];
        yield 'proposal is not a calculation' => ['proposal', 'POST', null];
        yield 'import preview is not a calculation' => ['import.preview', 'POST', null];
        yield 'repeat history is not a calculation' => ['history.repeat', 'POST', null];
        yield 'delete never emits result' => ['history.destroy', 'DELETE', null];
    }
}
