<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Domain\Rules;

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
    /** @return list<EmployeeSocialSecurityRule> */
    public static function employeeSocialSecurity(): array
    {
        $period = EffectivePeriod::from('2026-01-01', '2026-12-31');

        return [new EmployeeSocialSecurityRule(
            metadata: new NormativeRuleMetadata(
                identifier: 'net_salary.employee_social_security',
                version: new NormativeRuleVersion('2026.1.0'),
                effectivePeriod: $period,
                references: [new NormativeReference(
                    type: NormativeSourceType::Ordinance,
                    identifier: 'portaria-interministerial-mps-mf-13-2026',
                    title: 'Tabela de contribuição mensal de 2026 para empregado, empregado doméstico e trabalhador avulso',
                    publishedAt: ReferenceDate::fromString('2026-01-09'),
                    effectivePeriod: $period,
                    officialUrl: 'https://www.gov.br/inss/pt-br/direitos-e-deveres/inscricao-e-contribuicao/tabela-de-contribuicao-mensal',
                )],
                verifiedAt: ReferenceDate::fromString('2026-07-27'),
                verifiedBy: 'Prazzu Tools',
            ),
            brackets: [
                new SocialSecurityBracket(Money::fromMinor(0), Money::fromMinor(162100), Percentage::fromString('7.5')),
                new SocialSecurityBracket(Money::fromMinor(162100), Money::fromMinor(290284), Percentage::fromString('9')),
                new SocialSecurityBracket(Money::fromMinor(290284), Money::fromMinor(435427), Percentage::fromString('12')),
                new SocialSecurityBracket(Money::fromMinor(435427), Money::fromMinor(847555), Percentage::fromString('14')),
            ],
            maximumContributionBase: Money::fromMinor(847555),
        )];
    }

    /** @return list<MonthlyPersonalIncomeTaxRule> */
    public static function monthlyIrrf(): array
    {
        return [MonthlyPersonalIncomeTaxRule::for2026()];
    }
}
