<?php

declare(strict_types=1);

namespace App\Core\Analytics\Domain\Services;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;

final class AnalyticsEventNameResolver
{
    /** @var array<string, string> */
    private const LEGACY_ALIASES = [
        'blog_post_view' => AnalyticsEventName::BlogPostViewed->value,
        'blog_reading_started' => AnalyticsEventName::BlogReadingStarted->value,
        'blog_reading_completed' => AnalyticsEventName::BlogReadingCompleted->value,
        'blog_abandoned' => AnalyticsEventName::BlogReadingAbandoned->value,
        'blog_share' => AnalyticsEventName::BlogShared->value,
        'blog_download' => AnalyticsEventName::BlogDownloaded->value,
        'blog_comment' => AnalyticsEventName::BlogCommented->value,
        'blog_tool_click' => AnalyticsEventName::BlogToolClicked->value,
        'blog_scroll' => AnalyticsEventName::BlogScrollMeasured->value,
        'blog_time_spent' => AnalyticsEventName::BlogTimeSpent->value,
        'tool.calculation_started' => AnalyticsEventName::ToolCalculationStarted->value,
        'tool.calculation_completed' => AnalyticsEventName::ToolCalculationCompleted->value,
        'tool.time_spent' => AnalyticsEventName::ToolTimeSpent->value,
        'tool.history_viewed' => AnalyticsEventName::ToolHistoryViewed->value,
        'tool.plus_used' => AnalyticsEventName::ToolPlusUsed->value,
        'tool.exported' => AnalyticsEventName::ToolResultExported->value,
        'result.exported' => AnalyticsEventName::ToolResultExported->value,
        'tool.shared' => AnalyticsEventName::ToolResultShared->value,
        'user.registered' => AnalyticsEventName::AccountCreated->value,
        'plus.subscribed' => AnalyticsEventName::SubscriptionStarted->value,
        'business_document_validator.batch_processed' => AnalyticsEventName::BusinessDocumentValidatorBatchProcessed->value,
        'business_document_validator.batch_exported' => AnalyticsEventName::BusinessDocumentValidatorBatchExported->value,
    ];

    /** @return array<string, string> */
    public function legacyAliases(): array
    {
        return self::LEGACY_ALIASES;
    }

    public function canonical(string|AnalyticsEventName $eventName): string
    {
        $value = $eventName instanceof AnalyticsEventName ? $eventName->value : $eventName;

        return self::LEGACY_ALIASES[$value] ?? $value;
    }

    public function isCanonical(string $eventName): bool
    {
        return in_array($eventName, AnalyticsEventName::values(), true);
    }

    public function isKnown(string $eventName): bool
    {
        return $this->isCanonical($eventName) || array_key_exists($eventName, self::LEGACY_ALIASES);
    }

    /** @return list<string> */
    public function aliasesFor(string|AnalyticsEventName $event): array
    {
        $canonical = $this->canonical($event);
        $aliases = [];

        foreach (self::LEGACY_ALIASES as $legacy => $resolved) {
            if ($resolved === $canonical) {
                $aliases[] = $legacy;
            }
        }

        return $aliases;
    }

    /** @return list<string> */
    public function acceptedNamesFor(string|AnalyticsEventName $event): array
    {
        return array_values(array_unique([
            $this->canonical($event),
            ...$this->aliasesFor($event),
        ]));
    }

    /** @param iterable<string|AnalyticsEventName> $events
     *  @return list<string>
     */
    public function expand(iterable $events): array
    {
        $expanded = [];

        foreach ($events as $event) {
            $expanded = [...$expanded, ...$this->acceptedNamesFor($event)];
        }

        return array_values(array_unique($expanded));
    }

    /** @return array<string, string> */
    public function aliases(): array
    {
        return self::LEGACY_ALIASES;
    }
}
