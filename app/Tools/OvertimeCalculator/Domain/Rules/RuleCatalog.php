<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Domain\Rules;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Money\Percentage;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;

final class RuleCatalog
{
    /** @return list<LaborCompensationRule> */
    public static function laborCompensation(): array
    {
        $period = EffectivePeriod::from('2026-01-01', '2026-12-31');

        return [new LaborCompensationRule(
            metadata: new NormativeRuleMetadata(
                identifier: 'overtime.labor_compensation',
                version: new NormativeRuleVersion('2026.1.0'),
                effectivePeriod: $period,
                references: [
                    new NormativeReference(NormativeSourceType::Law, 'clt-art-59-73', 'CLT — arts. 59 e 73: horas extras e trabalho noturno', ReferenceDate::fromString('1943-05-01'), $period, 'https://www.planalto.gov.br/ccivil_03/decreto-lei/del5452compilado.htm'),
                    new NormativeReference(NormativeSourceType::Law, 'lei-605-1949-art-7', 'Lei nº 605/1949 — repouso semanal remunerado', ReferenceDate::fromString('1949-01-05'), $period, 'https://www.planalto.gov.br/ccivil_03/leis/l0605.htm'),
                    new NormativeReference(NormativeSourceType::CourtDecision, 'tst-tema-256', 'TST Tema 256 — horas extras habituais no repouso remunerado', ReferenceDate::fromString('2025-09-02'), $period, 'https://www.tst.jus.br/documents/d/guest/irr256temprandomsuffixfyalrpeo'),
                ],
                verifiedAt: ReferenceDate::fromString('2026-07-27'),
                verifiedBy: 'Prazzu Tools',
            ),
            minimumOvertimePremium: Percentage::fromString('50'),
            minimumNightPremium: Percentage::fromString('20'),
            reducedNightHourSeconds: 3150,
        )];
    }
}
