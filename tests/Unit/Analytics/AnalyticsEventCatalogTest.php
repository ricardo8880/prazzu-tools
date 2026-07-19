<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Domain\Catalog\AnalyticsEventCatalog;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use PHPUnit\Framework\TestCase;

final class AnalyticsEventCatalogTest extends TestCase
{
    public function test_every_official_event_has_a_semantic_definition(): void
    {
        $catalog = new AnalyticsEventCatalog;

        foreach (AnalyticsEventName::cases() as $event) {
            $definition = $catalog->describe($event);

            self::assertTrue($definition['known'], "Evento sem definição: {$event->value}");
            self::assertNotSame($event->value, $definition['label']);
            self::assertNotSame('', $definition['category']);
            self::assertNotSame('', $definition['description']);
            self::assertNotSame('', $definition['business_meaning']);
        }
    }

    public function test_legacy_event_keeps_its_technical_name_and_uses_the_canonical_meaning(): void
    {
        $definition = (new AnalyticsEventCatalog)->describe('tool.calculation_completed');

        self::assertSame('Cálculo concluído', $definition['label']);
        self::assertSame('tool.calculation.completed', $definition['key']);
        self::assertSame('tool.calculation_completed', $definition['technical_name']);
    }

    public function test_unknown_event_has_a_safe_fallback(): void
    {
        $definition = (new AnalyticsEventCatalog)->describe('integration.custom-event');

        self::assertFalse($definition['known']);
        self::assertSame('integration.custom-event', $definition['label']);
        self::assertSame('Outros eventos', $definition['category']);
    }
}
