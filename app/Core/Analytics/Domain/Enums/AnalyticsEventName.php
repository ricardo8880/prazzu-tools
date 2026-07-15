<?php

declare(strict_types=1);

namespace App\Core\Analytics\Domain\Enums;

enum AnalyticsEventName: string
{
    case PageViewed = 'page.viewed';

    case BlogPostViewed = 'blog.post.viewed';
    case BlogReadingStarted = 'blog.reading.started';
    case BlogReadingCompleted = 'blog.reading.completed';
    case BlogReadingAbandoned = 'blog.reading.abandoned';
    case BlogShared = 'blog.shared';
    case BlogDownloaded = 'blog.downloaded';
    case BlogCommented = 'blog.commented';
    case BlogToolClicked = 'blog.tool.clicked';
    case BlogScrollMeasured = 'blog.scroll.measured';
    case BlogTimeSpent = 'blog.time.spent';

    case ToolOpened = 'tool.opened';
    case ToolViewed = 'tool.viewed';
    case ToolCalculationStarted = 'tool.calculation.started';
    case ToolCalculationCompleted = 'tool.calculation.completed';
    case ToolTimeSpent = 'tool.time.spent';
    case ToolHistoryViewed = 'tool.history.viewed';
    case ToolPlusUsed = 'tool.plus.used';
    case ToolResultExported = 'tool.result.exported';
    case ToolResultShared = 'tool.result.shared';

    case AccountCreated = 'account.created';
    case SubscriptionStarted = 'subscription.started';
    case SubscriptionCreated = 'subscription.created';

    case BusinessDocumentValidatorBatchProcessed = 'business-document-validator.batch.processed';
    case BusinessDocumentValidatorBatchExported = 'business-document-validator.batch.exported';

    /** @return list<string> */
    public static function values(): array
    {
        return array_map(
            static fn (self $event): string => $event->value,
            self::cases(),
        );
    }
}
