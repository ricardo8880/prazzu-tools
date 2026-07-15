<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AnalyticsCurrentBehaviorTest extends TestCase
{
    #[DataProvider('currentlyAcceptedEventNames')]
    public function test_current_event_names_remain_accepted_during_the_transition(string $eventName): void
    {
        $event = AnalyticsEvent::make($eventName, 'platform');

        self::assertSame($eventName, $event->name);
    }

    /** @return iterable<string, array{string}> */
    public static function currentlyAcceptedEventNames(): iterable
    {
        yield 'page view oficial' => ['page.viewed'];
        yield 'blog view legado' => ['blog_post_view'];
        yield 'blog reading legado' => ['blog_reading_started'];
        yield 'tool opened oficial' => ['tool.opened'];
        yield 'tool calculation legado' => ['tool.calculation_started'];
        yield 'batch tool legado' => ['business_document_validator.batch_processed'];
        yield 'registro alternativo' => ['user.registered'];
        yield 'assinatura alternativa' => ['plus.subscribed'];
    }
}
