<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Domain\Rules;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Money\Percentage;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;

final class RuleCatalog
{
    public const LATE_PAYMENT_IDENTIFIER = 'federal_payment_guide.late_payment_charges';
    public const CURRENT_VERSION = '2026.1.0';

    /** @return list<LatePaymentRule> */
    public static function latePaymentCharges(): array
    {
        $period = EffectivePeriod::from('2009-05-28');

        return [new LatePaymentRule(
            metadata: new NormativeRuleMetadata(
                identifier: self::LATE_PAYMENT_IDENTIFIER,
                version: new NormativeRuleVersion(self::CURRENT_VERSION),
                effectivePeriod: $period,
                references: [
                    new NormativeReference(
                        type: NormativeSourceType::Law,
                        identifier: 'lei-11941-2009-art-26',
                        title: 'Lei nº 11.941/2009 — acréscimos legais sobre débitos federais',
                        publishedAt: ReferenceDate::fromString('2009-05-28'),
                        effectivePeriod: $period,
                        officialUrl: 'https://www.planalto.gov.br/ccivil_03/_ato2007-2010/2009/lei/l11941.htm',
                        article: 'Art. 26',
                    ),
                    new NormativeReference(
                        type: NormativeSourceType::Other,
                        identifier: 'receita-federal-calculo-multa-mora',
                        title: 'Receita Federal — Como calcular multa de mora',
                        publishedAt: ReferenceDate::fromString('2015-04-28'),
                        effectivePeriod: $period,
                        officialUrl: 'https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/pagamentos-e-parcelamentos/pagamento-em-atraso/como-calcular-multa-de-mora-acrescimos-legais',
                    ),
                    new NormativeReference(
                        type: NormativeSourceType::Other,
                        identifier: 'receita-federal-calculo-juros-mora',
                        title: 'Receita Federal — Como calcular juros de mora',
                        publishedAt: ReferenceDate::fromString('2015-03-06'),
                        effectivePeriod: $period,
                        officialUrl: 'https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/pagamentos-e-parcelamentos/pagamento-em-atraso/como-calcular-juros-de-mora-acrescimos-legais',
                    ),
                ],
                verifiedAt: ReferenceDate::fromString('2026-07-21'),
                verifiedBy: 'Prazzu Tools',
            ),
            dailyPenaltyRate: Percentage::fromString('0.33'),
            maximumPenaltyRate: Percentage::fromString('20'),
        )];
    }
}
