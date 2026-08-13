<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Domain\Rules;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;
use App\Core\Tax\Normative\MonthlyPersonalIncomeTaxRule;

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

    /** @return list<MonthlyPersonalIncomeTaxRule> */
    public static function monthlyIrrf(): array
    {
        return [MonthlyPersonalIncomeTaxRule::for2026()];
    }
}
