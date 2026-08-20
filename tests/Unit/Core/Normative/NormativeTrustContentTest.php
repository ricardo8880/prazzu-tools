<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Normative\Application\NormativeTrustContent;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Normative\NormativeSourceType;
use PHPUnit\Framework\TestCase;

final class NormativeTrustContentTest extends TestCase
{
    public function test_it_prepares_human_readable_rule_verification_and_official_sources(): void
    {
        $snapshot = new NormativeRuleSnapshot(
            identifier: 'tax.example_2026',
            version: '2026.1.0',
            referenceDate: '2026-08-01',
            effectiveFrom: '2026-01-01',
            effectiveUntil: '2026-12-31',
            verifiedAt: '2026-08-10',
            verifiedBy: 'Equipe Prazzu',
            references: [
                (new NormativeReference(
                    type: NormativeSourceType::Other,
                    identifier: 'Ato 123/2026',
                    title: 'Ato oficial de exemplo',
                    publishedAt: ReferenceDate::fromString('2026-01-01'),
                    effectivePeriod: EffectivePeriod::from('2026-01-01'),
                    officialUrl: 'https://example.gov.br/ato-123',
                ))->toArray(),
            ],
        );

        $content = (new NormativeTrustContent)->for(
            [$snapshot],
            ['A alíquota específica precisa ser confirmada para o caso concreto.'],
            true,
        );

        self::assertNotNull($content);
        self::assertTrue($content['is_estimate']);
        self::assertSame(1, $content['source_count']);
        self::assertSame('01/08/2026', $content['rules'][0]['reference_date']);
        self::assertSame('01/01/2026', $content['rules'][0]['effective_from']);
        self::assertSame('31/12/2026', $content['rules'][0]['effective_until']);
        self::assertSame('10/08/2026', $content['rules'][0]['verified_at']);
        self::assertSame('https://example.gov.br/ato-123', $content['rules'][0]['references'][0]['official_url']);
        self::assertSame(['A alíquota específica precisa ser confirmada para o caso concreto.'], $content['assumptions']);
    }

    public function test_it_omits_the_surface_when_no_official_normative_source_exists(): void
    {
        self::assertNull((new NormativeTrustContent)->for([]));
        self::assertNull((new NormativeTrustContent)->for([
            [
                'identifier' => 'rule.without.source',
                'version' => '1.0.0',
                'references' => [],
            ],
        ]));
    }
}
