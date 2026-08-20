<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Domain\Rules;

use App\Core\Dates\Contracts\EffectiveDated;
use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;
use InvalidArgumentException;

final readonly class ConsumptionTaxTransitionRule implements EffectiveDated, NormativeRule
{
    public const IDENTIFIER = 'tax_reform.consumption_transition';

    public const VERSION = '2026.08.3';

    private const VERIFIED_AT = '2026-08-19';

    /** @return array{lf:int,ls:int,ibs:int,note:string} */
    public function forYear(int $year): array
    {
        if ($year < 2026 || $year > 2033) {
            throw new InvalidArgumentException('Ano inválido.');
        }

        return match ($year) {
            2026 => [
                'lf' => 100,
                'ls' => 100,
                'ibs' => 0,
                'note' => 'Ano-teste: CBS 0,9% e IBS 0,1%, com compensação do montante recolhido e hipótese legal de dispensa vinculada ao cumprimento das obrigações acessórias.',
            ],
            2027, 2028 => [
                'lf' => 0,
                'ls' => 100,
                'ibs' => 0,
                'note' => 'PIS/Cofins extintos; CBS de referência reduzida em 0,1 p.p. e IBS de 0,1%.',
            ],
            2029 => ['lf' => 0, 'ls' => 90, 'ibs' => 10, 'note' => 'ICMS/ISS 90% e IBS 10%.'],
            2030 => ['lf' => 0, 'ls' => 80, 'ibs' => 20, 'note' => 'ICMS/ISS 80% e IBS 20%.'],
            2031 => ['lf' => 0, 'ls' => 70, 'ibs' => 30, 'note' => 'ICMS/ISS 70% e IBS 30%.'],
            2032 => ['lf' => 0, 'ls' => 60, 'ibs' => 40, 'note' => 'ICMS/ISS 60% e IBS 40%.'],
            2033 => ['lf' => 0, 'ls' => 0, 'ibs' => 100, 'note' => 'Novo modelo integral; ICMS/ISS extintos.'],
        };
    }

    public function normativeMetadata(): NormativeRuleMetadata
    {
        $period = $this->effectivePeriod();

        return new NormativeRuleMetadata(
            identifier: self::IDENTIFIER,
            version: new NormativeRuleVersion(self::VERSION),
            effectivePeriod: $period,
            references: [
                new NormativeReference(
                    type: NormativeSourceType::Constitution,
                    identifier: 'ec-132-2023',
                    title: 'Emenda Constitucional nº 132/2023 — Reforma Tributária do Consumo',
                    publishedAt: ReferenceDate::fromString('2023-12-20'),
                    effectivePeriod: $period,
                    officialUrl: 'https://www.planalto.gov.br/ccivil_03/constituicao/emendas/emc/emc132.htm',
                ),
                new NormativeReference(
                    type: NormativeSourceType::ComplementaryLaw,
                    identifier: 'lc-214-2025',
                    title: 'Lei Complementar nº 214/2025 — IBS, CBS e Imposto Seletivo',
                    publishedAt: ReferenceDate::fromString('2025-01-16'),
                    effectivePeriod: $period,
                    officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp214.htm',
                ),
                new NormativeReference(
                    type: NormativeSourceType::Other,
                    identifier: 'receita-reforma-consumo-entenda',
                    title: 'Receita Federal — Entenda a Reforma Tributária do Consumo',
                    publishedAt: ReferenceDate::fromString('2025-06-27'),
                    effectivePeriod: $period,
                    officialUrl: 'https://www.gov.br/receitafederal/pt-br/acesso-a-informacao/acoes-e-programas/programas-e-atividades/reforma-tributaria-do-consumo/entenda',
                ),
            ],
            verifiedAt: ReferenceDate::fromString(self::VERIFIED_AT),
            verifiedBy: 'Prazzu Tools',
        );
    }

    public function effectivePeriod(): EffectivePeriod
    {
        return EffectivePeriod::from('2026-01-01', '2033-12-31');
    }
}
