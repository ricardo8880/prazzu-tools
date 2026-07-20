<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\Exceptions\OverlappingEffectiveRules;
use App\Core\Dates\ReferenceDate;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\Exceptions\DuplicateNormativeRuleVersion;
use App\Core\Normative\Exceptions\NormativeRuleNotFound;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleResolver;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;
use PHPUnit\Framework\TestCase;

final class NormativeRuleResolverTest extends TestCase
{
    public function test_it_resolves_the_rule_effective_on_the_reference_date(): void
    {
        $old = $this->rule('1.0.0', '2024-01-01', '2024-12-31');
        $current = $this->rule('2.0.0', '2025-01-01');

        $resolved = (new NormativeRuleResolver)->resolveCurrent(
            [$old, $current],
            'tributo.faixa',
            ReferenceDate::fromString('2026-07-20'),
        );

        self::assertSame($current, $resolved);
    }

    public function test_it_reproduces_an_exact_historical_version(): void
    {
        $old = $this->rule('1.0.0', '2024-01-01', '2024-12-31');
        $current = $this->rule('2.0.0', '2025-01-01');

        $resolved = (new NormativeRuleResolver)->resolveHistorical(
            [$old, $current],
            'tributo.faixa',
            new NormativeRuleVersion('1.0.0'),
            ReferenceDate::fromString('2024-06-01'),
        );

        self::assertSame($old, $resolved);
    }

    public function test_it_rejects_a_historical_version_outside_its_effective_period(): void
    {
        $this->expectException(NormativeRuleNotFound::class);

        (new NormativeRuleResolver)->resolveHistorical(
            [$this->rule('1.0.0', '2024-01-01', '2024-12-31')],
            'tributo.faixa',
            new NormativeRuleVersion('1.0.0'),
            ReferenceDate::fromString('2025-01-01'),
        );
    }

    public function test_it_rejects_duplicate_versions(): void
    {
        $this->expectException(DuplicateNormativeRuleVersion::class);

        (new NormativeRuleResolver)->validatedCatalog([
            $this->rule('1.0.0', '2024-01-01', '2024-12-31'),
            $this->rule('1.0.0', '2025-01-01'),
        ]);
    }

    public function test_it_rejects_overlapping_periods_for_the_same_rule(): void
    {
        $this->expectException(OverlappingEffectiveRules::class);

        (new NormativeRuleResolver)->validatedCatalog([
            $this->rule('1.0.0', '2024-01-01', '2025-01-31'),
            $this->rule('2.0.0', '2025-01-01'),
        ]);
    }

    private function rule(string $version, string $startsAt, ?string $endsAt = null): TestNormativeRule
    {
        return new TestNormativeRule(new NormativeRuleMetadata(
            identifier: 'tributo.faixa',
            version: new NormativeRuleVersion($version),
            effectivePeriod: EffectivePeriod::from($startsAt, $endsAt),
            references: [new NormativeReference(
                NormativeSourceType::OfficialTable,
                'Tabela oficial de exemplo',
                'Tabela usada somente no teste',
                ReferenceDate::fromString('2024-01-01'),
                officialUrl: 'https://www.gov.br/exemplo',
            )],
            verifiedAt: ReferenceDate::fromString('2025-02-01'),
            verifiedBy: 'Equipe fiscal',
        ));
    }
}

final readonly class TestNormativeRule implements NormativeRule
{
    public function __construct(private NormativeRuleMetadata $metadata) {}

    public function normativeMetadata(): NormativeRuleMetadata
    {
        return $this->metadata;
    }

    public function effectivePeriod(): EffectivePeriod
    {
        return $this->metadata->effectivePeriod;
    }
}
