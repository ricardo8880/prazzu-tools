<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AnalyticsEventNameTest extends TestCase
{
    public function test_every_catalog_event_follows_the_official_dot_notation(): void
    {
        foreach (AnalyticsEventName::cases() as $event) {
            self::assertMatchesRegularExpression(
                '/^[a-z][a-z0-9-]*(?:\.[a-z][a-z0-9-]*)+$/',
                $event->value,
                "Evento fora do padrão oficial: {$event->value}",
            );

            self::assertSame($event->value, (new AnalyticsEvent($event->value, 'platform'))->name);
        }
    }

    #[DataProvider('legacyEventProvider')]
    public function test_legacy_names_resolve_to_their_canonical_name(string $legacy, AnalyticsEventName $canonical): void
    {
        $resolver = new AnalyticsEventNameResolver();

        self::assertSame($canonical->value, $resolver->canonical($legacy));
        self::assertTrue($resolver->isKnown($legacy));
        self::assertContains($legacy, $resolver->aliasesFor($canonical));
    }

    public function test_expands_canonical_names_with_all_legacy_aliases(): void
    {
        $resolver = new AnalyticsEventNameResolver();

        self::assertSame([
            'blog.post.viewed',
            'blog_post_view',
            'tool.result.exported',
            'tool.exported',
            'result.exported',
        ], $resolver->expand([
            AnalyticsEventName::BlogPostViewed,
            AnalyticsEventName::ToolResultExported,
        ]));
    }

    public function test_unknown_names_are_preserved_for_backward_compatibility(): void
    {
        $resolver = new AnalyticsEventNameResolver();

        self::assertSame('integration.custom-event', $resolver->canonical('integration.custom-event'));
        self::assertFalse($resolver->isKnown('integration.custom-event'));
    }

    /** @return iterable<string, array{string, AnalyticsEventName}> */
    public static function legacyEventProvider(): iterable
    {
        yield 'visualização de artigo' => ['blog_post_view', AnalyticsEventName::BlogPostViewed];
        yield 'início de leitura' => ['blog_reading_started', AnalyticsEventName::BlogReadingStarted];
        yield 'conclusão de leitura' => ['blog_reading_completed', AnalyticsEventName::BlogReadingCompleted];
        yield 'início de cálculo' => ['tool.calculation_started', AnalyticsEventName::ToolCalculationStarted];
        yield 'conclusão de cálculo' => ['tool.calculation_completed', AnalyticsEventName::ToolCalculationCompleted];
        yield 'cadastro legado' => ['user.registered', AnalyticsEventName::AccountCreated];
        yield 'assinatura legada' => ['plus.subscribed', AnalyticsEventName::SubscriptionStarted];
    }
}
