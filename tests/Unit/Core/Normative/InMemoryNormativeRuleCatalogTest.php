<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;
use App\Core\Normative\Services\InMemoryNormativeRuleCatalog;
use PHPUnit\Framework\TestCase;

final class InMemoryNormativeRuleCatalogTest extends TestCase
{
    public function test_it_resolves_and_snapshots_a_versioned_rule(): void
    {
        $rule = new CatalogTestRule(new NormativeRuleMetadata(
            identifier: 'labor.inss',
            version: new NormativeRuleVersion('1.0.0'),
            effectivePeriod: EffectivePeriod::from('2026-01-01'),
            references: [new NormativeReference(
                NormativeSourceType::OfficialTable,
                'official-table-2026',
                'Tabela oficial de teste',
                ReferenceDate::fromString('2026-01-01'),
                officialUrl: 'https://www.gov.br/exemplo',
            )],
            verifiedAt: ReferenceDate::fromString('2026-01-02'),
            verifiedBy: 'Equipe fiscal',
        ));

        $date = ReferenceDate::fromString('2026-07-25');
        $resolved = (new InMemoryNormativeRuleCatalog([$rule]))->current('labor.inss', $date);
        $snapshot = NormativeRuleSnapshot::fromRule($resolved, $date)->toArray();

        self::assertSame('1.0.0', $snapshot['version']);
        self::assertSame('2026-07-25', $snapshot['reference_date']);
        self::assertSame('https://www.gov.br/exemplo', $snapshot['references'][0]['official_url']);
    }
}

final readonly class CatalogTestRule implements NormativeRule
{
    public function __construct(private NormativeRuleMetadata $metadata) {}
    public function normativeMetadata(): NormativeRuleMetadata { return $this->metadata; }
    public function effectivePeriod(): EffectivePeriod { return $this->metadata->effectivePeriod; }
}
