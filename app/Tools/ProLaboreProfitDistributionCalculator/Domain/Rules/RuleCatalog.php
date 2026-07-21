<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Rules;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;

final class RuleCatalog
{
    /** @return list<SocialSecurityRule> */
    public static function socialSecurity(): array
    {
        $period = EffectivePeriod::from('2026-01-01', '2026-12-31');

        return [new SocialSecurityRule(
            metadata: new NormativeRuleMetadata(
                identifier: 'pro_labore.social_security',
                version: new NormativeRuleVersion('2026.1.0'),
                effectivePeriod: $period,
                references: [new NormativeReference(
                    type: NormativeSourceType::Ordinance,
                    identifier: 'portaria-interministerial-mps-mf-13-2026',
                    title: 'Tabela de contribuição mensal de 2026',
                    publishedAt: ReferenceDate::fromString('2026-01-09'),
                    effectivePeriod: $period,
                    officialUrl: 'https://www.gov.br/inss/pt-br/direitos-e-deveres/inscricao-e-contribuicao/tabela-de-contribuicao-mensal',
                )],
                verifiedAt: ReferenceDate::fromString('2026-07-21'),
                verifiedBy: 'Prazzu Tools',
            ),
            minimumContributionBase: Money::fromMinor(162100),
            maximumContributionBase: Money::fromMinor(847555),
            withholdingRate: Percentage::fromString('11'),
            employerRate: Percentage::fromString('20'),
        )];
    }

    /** @return list<MonthlyIrrfRule> */
    public static function monthlyIrrf(): array
    {
        $period = EffectivePeriod::from('2026-01-01', '2026-12-31');

        return [new MonthlyIrrfRule(
            metadata: new NormativeRuleMetadata(
                identifier: 'pro_labore.monthly_irrf',
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
                verifiedAt: ReferenceDate::fromString('2026-07-21'),
                verifiedBy: 'Prazzu Tools',
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
        )];
    }
}
