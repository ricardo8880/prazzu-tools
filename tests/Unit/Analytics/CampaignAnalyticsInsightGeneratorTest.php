<?php

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Application\Services\CampaignAnalyticsInsightGenerator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

final class CampaignAnalyticsInsightGeneratorTest extends TestCase
{
    public function test_it_generates_campaign_growth_and_keyword_insights(): void
    {
        $current = [
            'summary' => ['sessions' => 20, 'conversion_rate' => 30.0],
            'keywords' => collect([(object) ['label' => 'das mei', 'sessions' => 10, 'conversion_rate' => 40.0]]),
            'campaigns' => collect(),
        ];
        $previous = [
            'summary' => ['sessions' => 10, 'conversion_rate' => 20.0],
            'keywords' => new Collection(), 'campaigns' => new Collection(),
        ];

        $insights = (new CampaignAnalyticsInsightGenerator())->generate($current, $previous);

        self::assertTrue($insights->contains(fn (object $item): bool => $item->title === 'Campanhas ganharam tráfego'));
        self::assertTrue($insights->contains(fn (object $item): bool => $item->title === 'Palavra-chave com melhor eficiência'));
    }
}
