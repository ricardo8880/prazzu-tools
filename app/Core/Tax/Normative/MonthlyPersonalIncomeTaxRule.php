<?php

declare(strict_types=1);

namespace App\Core\Tax\Normative;

use App\Core\Dates\Contracts\EffectiveDated;
use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;

final readonly class MonthlyPersonalIncomeTaxRule implements EffectiveDated, NormativeRule
{
    /** @param list<MonthlyIrrfBracket> $brackets */
    private function __construct(
        private NormativeRuleMetadata $metadata,
        public array $brackets,
        public Money $dependentDeduction,
        public Money $simplifiedDeduction,
        public Money $fullReductionIncomeLimit,
        public Money $fullReductionCap,
        public Money $partialReductionIncomeLimit,
        public Money $partialReductionFixedAmount,
        public int $partialReductionCoefficientMillionths,
    ) {}

    public static function for2026(): self
    {
        $period = EffectivePeriod::from('2026-01-01', '2026-12-31');

        return new self(
            metadata: new NormativeRuleMetadata(
                identifier: 'tax.irrf.monthly',
                version: new NormativeRuleVersion('2026.1.0'),
                effectivePeriod: $period,
                references: [new NormativeReference(
                    type: NormativeSourceType::Law,
                    identifier: 'lei-15270-2025',
                    title: 'Tributação mensal do imposto sobre a renda em 2026',
                    publishedAt: ReferenceDate::fromString('2025-11-26'),
                    effectivePeriod: $period,
                    officialUrl: 'https://www.gov.br/receitafederal/pt-br/assuntos/meu-imposto-de-renda/tabelas/2026',
                )],
                verifiedAt: ReferenceDate::fromString('2026-07-27'),
                verifiedBy: 'Prazzu Tools — expansão Lote 11',
            ),
            brackets: [
                new MonthlyIrrfBracket(Money::fromMinor(242880), Percentage::zero(), Money::zero()),
                new MonthlyIrrfBracket(Money::fromMinor(282665), Percentage::fromString('7.5'), Money::fromMinor(18216)),
                new MonthlyIrrfBracket(Money::fromMinor(375105), Percentage::fromString('15'), Money::fromMinor(39416)),
                new MonthlyIrrfBracket(Money::fromMinor(466468), Percentage::fromString('22.5'), Money::fromMinor(67549)),
                new MonthlyIrrfBracket(null, Percentage::fromString('27.5'), Money::fromMinor(90873)),
            ],
            dependentDeduction: Money::fromMinor(18959),
            simplifiedDeduction: Money::fromMinor(60720),
            fullReductionIncomeLimit: Money::fromMinor(500000),
            fullReductionCap: Money::fromMinor(31289),
            partialReductionIncomeLimit: Money::fromMinor(735000),
            partialReductionFixedAmount: Money::fromMinor(97862),
            partialReductionCoefficientMillionths: 133145,
        );
    }

    public function normativeMetadata(): NormativeRuleMetadata
    {
        return $this->metadata;
    }

    public function effectivePeriod(): EffectivePeriod
    {
        return $this->metadata->effectivePeriod;
    }

    public function snapshot(ReferenceDate $referenceDate): NormativeRuleSnapshot
    {
        return NormativeRuleSnapshot::fromRule($this, $referenceDate);
    }
}
