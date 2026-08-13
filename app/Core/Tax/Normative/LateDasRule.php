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

final readonly class LateDasRule implements NormativeRule
{
    public const VERSION = '1.0.0';

    public function dailyFine(): Percentage
    {
        return Percentage::fromString('0.33');
    }

    public function maximumFine(): Percentage
    {
        return Percentage::fromString('20');
    }

    public function paymentMonthInterest(): Percentage
    {
        return Percentage::fromString('1');
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
            identifier: 'tax.simples.late-das',
            version: new NormativeRuleVersion(self::VERSION),
            effectivePeriod: EffectivePeriod::from('2007-07-01'),
            references: [
                new NormativeReference(
                    type: NormativeSourceType::Law,
                    identifier: 'lc-123-art-35',
                    title: 'Lei Complementar nº 123/2006 — acréscimos legais do Simples Nacional',
                    publishedAt: ReferenceDate::fromString('2006-12-14'),
                    officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp123.htm',
                    article: 'Art. 35',
                ),
                new NormativeReference(
                    type: NormativeSourceType::Law,
                    identifier: 'lei-9430-art-61',
                    title: 'Lei nº 9.430/1996 — multa e juros de mora',
                    publishedAt: ReferenceDate::fromString('1996-12-27'),
                    officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/l9430.htm',
                    article: 'Art. 61',
                ),
            ],
            verifiedAt: ReferenceDate::fromString('2026-07-25'),
            verifiedBy: 'Prazzu Tools — Lote 6',
        );
    }
}
