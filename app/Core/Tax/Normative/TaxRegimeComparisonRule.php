<?php

declare(strict_types=1);

namespace App\Core\Tax\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;

final readonly class TaxRegimeComparisonRule implements NormativeRule
{
    public const VERSION = '1.0.0';

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
            identifier: 'tax.regime-comparison',
            version: new NormativeRuleVersion(self::VERSION),
            effectivePeriod: EffectivePeriod::from('2007-07-01'),
            references: [
                new NormativeReference(type: NormativeSourceType::Law, identifier: 'lc-123', title: 'Lei Complementar nº 123/2006 — Simples Nacional', publishedAt: ReferenceDate::fromString('2006-12-14'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp123.htm'),
                new NormativeReference(type: NormativeSourceType::Law, identifier: 'lei-9249', title: 'Lei nº 9.249/1995 — IRPJ e CSLL', publishedAt: ReferenceDate::fromString('1995-12-26'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/l9249.htm'),
                new NormativeReference(type: NormativeSourceType::Law, identifier: 'lei-9718', title: 'Lei nº 9.718/1998 — PIS e Cofins', publishedAt: ReferenceDate::fromString('1998-11-27'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/l9718.htm'),
            ],
            verifiedAt: ReferenceDate::fromString('2026-07-25'),
            verifiedBy: 'Prazzu Tools — Lote 6',
        );
    }
}
