<?php

declare(strict_types=1);

namespace App\Core\Labor\Normative;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Money\Percentage;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;
use InvalidArgumentException;

final readonly class EmployerChargeRule implements NormativeRule
{
    public const VERSION = '1.0.0';

    public function ratesFor(string $regime, Percentage $rat, Percentage $thirdParties): EmployerChargeRates
    {
        if (! in_array($regime, ['general', 'simples_annex_iv', 'simples_other'], true)) {
            throw new InvalidArgumentException('Regime não suportado pela regra de encargos patronais.');
        }

        return new EmployerChargeRates(
            fgts: Percentage::fromString('8'),
            cpp: $regime === 'simples_other' ? Percentage::zero() : Percentage::fromString('20'),
            rat: $regime === 'simples_other' ? Percentage::zero() : $rat,
            thirdParties: str_starts_with($regime, 'simples_') ? Percentage::zero() : $thirdParties,
        );
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
            identifier: 'labor.employer-charges',
            version: new NormativeRuleVersion(self::VERSION),
            effectivePeriod: EffectivePeriod::from('2007-07-01'),
            references: [
                new NormativeReference(
                    type: NormativeSourceType::Law,
                    identifier: 'lei-8212-art-22',
                    title: 'Lei nº 8.212/1991 — contribuição da empresa',
                    publishedAt: ReferenceDate::fromString('1991-07-25'),
                    officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/l8212compilado.htm',
                    article: 'Art. 22, incisos I e II',
                ),
                new NormativeReference(
                    type: NormativeSourceType::Law,
                    identifier: 'lei-8036-art-15',
                    title: 'Lei nº 8.036/1990 — depósitos do FGTS',
                    publishedAt: ReferenceDate::fromString('1990-05-11'),
                    officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/l8036consol.htm',
                    article: 'Art. 15',
                ),
                new NormativeReference(
                    type: NormativeSourceType::Other,
                    identifier: 'rfb-simples-anexo-iv-cpp',
                    title: 'Contribuição Previdenciária — Anexo IV do Simples Nacional',
                    publishedAt: ReferenceDate::fromString('2016-09-25'),
                    officialUrl: 'https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cobrancas-e-intimacoes/contribuicao-previdenciaria-anexo-iv-do-simples-nacional',
                ),
            ],
            verifiedAt: ReferenceDate::fromString('2026-07-25'),
            verifiedBy: 'Prazzu Tools — Lote 3',
        );
    }
}
