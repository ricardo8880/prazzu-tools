<?php

declare(strict_types=1);

namespace App\Core\Analytics\Domain\Services;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;

final class ToolAnalyticsEventClassifier
{
    /** @var list<string> */
    private const RESULT_ACTIONS = [
        'calculate',
        'adjustments.calculate',
        'batch.calculate',
        'scenarios.simulate',
        'validate',
        'analyze-consistency',
        'lookup-company',
        'validate-state-registration',
    ];

    public function classify(string $action, string $method): ?AnalyticsEventName
    {
        $method = strtoupper($method);

        if ($method === 'GET' && $action === 'index') {
            return AnalyticsEventName::ToolOpened;
        }

        if (preg_match('/(^|\.)(export|pdf|print)(\.|$)/', $action) === 1) {
            return AnalyticsEventName::ToolResultExported;
        }

        if ($method === 'GET' && str_contains($action, 'history')) {
            return AnalyticsEventName::ToolHistoryViewed;
        }

        if (str_contains($action, 'share') && ! str_contains($action, 'revoke')) {
            return AnalyticsEventName::ToolResultShared;
        }

        if (str_starts_with($action, 'plus.')) {
            return AnalyticsEventName::ToolPlusUsed;
        }

        if ($method === 'POST' && in_array($action, self::RESULT_ACTIONS, true)) {
            return AnalyticsEventName::ToolCalculationCompleted;
        }

        return null;
    }
}
