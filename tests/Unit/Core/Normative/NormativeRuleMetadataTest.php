<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Exceptions\InvalidValue;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;
use PHPUnit\Framework\TestCase;

final class NormativeRuleMetadataTest extends TestCase
{
    public function test_it_serializes_the_complete_governance_contract(): void
    {
        $metadata = new NormativeRuleMetadata(
            identifier: 'simples-nacional.anexo-iii',
            version: new NormativeRuleVersion('1.2.0'),
            effectivePeriod: EffectivePeriod::from('2025-01-01'),
            references: [$this->officialReference()],
            verifiedAt: ReferenceDate::fromString('2025-01-10'),
            verifiedBy: 'Equipe fiscal',
        );

        self::assertSame('simples-nacional.anexo-iii', $metadata->toArray()['identifier']);
        self::assertSame('1.2.0', $metadata->toArray()['version']);
        self::assertSame('https://www.gov.br/exemplo', $metadata->toArray()['references'][0]['official_url']);
    }

    public function test_it_requires_an_official_source(): void
    {
        $this->expectException(InvalidValue::class);

        new NormativeRuleMetadata(
            identifier: 'regra.sem-fonte',
            version: new NormativeRuleVersion('1.0.0'),
            effectivePeriod: EffectivePeriod::from('2025-01-01'),
            references: [new NormativeReference(
                NormativeSourceType::Law,
                'Lei de exemplo',
                'Fonte sem URL oficial',
                ReferenceDate::fromString('2025-01-01'),
            )],
            verifiedAt: ReferenceDate::fromString('2025-01-10'),
            verifiedBy: 'Equipe fiscal',
        );
    }

    private function officialReference(): NormativeReference
    {
        return new NormativeReference(
            NormativeSourceType::Law,
            'Lei de exemplo',
            'Fonte oficial usada no teste',
            ReferenceDate::fromString('2025-01-01'),
            officialUrl: 'https://www.gov.br/exemplo',
        );
    }
}
