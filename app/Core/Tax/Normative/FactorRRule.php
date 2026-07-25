<?php

declare(strict_types=1);

namespace App\Core\Tax\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Money\Percentage;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;

final readonly class FactorRRule implements NormativeRule
{
    public const VERSION = '1.0.0';

    public function threshold(): Percentage
    {
        return Percentage::fromString('28');
    }

    public function snapshot(ReferenceDate $referenceDate): NormativeRuleSnapshot
    {
        return NormativeRuleSnapshot::fromRule($this, $referenceDate);
    }

    public function effectivePeriod(): EffectivePeriod
    {
        return $this->normativeMetadata()->effectivePeriod;
    }

    public function normativeMetadata(): NormativeRuleMetadata
    {
        return new NormativeRuleMetadata(
            identifier: 'tax.simples.factor-r',
            version: new NormativeRuleVersion(self::VERSION),
            effectivePeriod: EffectivePeriod::from('2018-01-01'),
            references: [
                new NormativeReference(
                    type: NormativeSourceType::Resolution,
                    identifier: 'cgsn-140-art-26',
                    title: 'Resolução CGSN nº 140/2018 — Fator R',
                    publishedAt: ReferenceDate::fromString('2018-05-22'),
                    officialUrl: 'https://normas.receita.fazenda.gov.br/sijut2consulta/link.action?idAto=92278',
                    article: 'Art. 26',
                ),
            ],
            verifiedAt: ReferenceDate::fromString('2026-07-25'),
            verifiedBy: 'Prazzu Tools — Lote 6',
        );
    }
}
