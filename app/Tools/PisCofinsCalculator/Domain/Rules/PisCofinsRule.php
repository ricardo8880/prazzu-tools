<?php

declare(strict_types=1);

namespace App\Tools\PisCofinsCalculator\Domain\Rules;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Money\Percentage;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;

final readonly class PisCofinsRule implements NormativeRule
{
    public function effectivePeriod(): EffectivePeriod { return $this->normativeMetadata()->effectivePeriod; }

    public function normativeMetadata(): NormativeRuleMetadata
    {
        return new NormativeRuleMetadata(
            identifier: 'pis_cofins.general_2026',
            version: new NormativeRuleVersion('2026.1.0'),
            effectivePeriod: EffectivePeriod::from('2026-01-01', '2026-12-31'),
            references: [
                new NormativeReference(NormativeSourceType::Law, 'Lei 9.718/1998', 'Regime cumulativo de PIS/Pasep e Cofins', ReferenceDate::fromString('1998-11-27'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/l9718.htm', article: 'arts. 2º, 3º e 8º'),
                new NormativeReference(NormativeSourceType::Law, 'Lei 10.637/2002', 'Regime não cumulativo do PIS/Pasep', ReferenceDate::fromString('2002-12-30'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/2002/l10637.htm', article: 'arts. 1º a 3º'),
                new NormativeReference(NormativeSourceType::Law, 'Lei 10.833/2003', 'Regime não cumulativo da Cofins', ReferenceDate::fromString('2003-12-29'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/2003/l10.833.htm', article: 'arts. 1º a 3º'),
                new NormativeReference(NormativeSourceType::ComplementaryLaw, 'LC 214/2025', 'Transição da CBS e do IBS', ReferenceDate::fromString('2025-01-16'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp214.htm'),
                new NormativeReference(NormativeSourceType::Other, 'RFB Reforma Tributária 2026', 'Orientações oficiais da transição da CBS/IBS em 2026', ReferenceDate::fromString('2025-12-12'), officialUrl: 'https://www.gov.br/receitafederal/pt-br/acesso-a-informacao/acoes-e-programas/programas-e-atividades/reforma-tributaria-do-consumo/orientacoes-2026'),
            ],
            verifiedAt: ReferenceDate::fromString('2026-08-10'), verifiedBy: 'Prazzu Tools',
        );
    }

    public function cumulativePisRate(): Percentage { return Percentage::fromString('0.65'); }
    public function cumulativeCofinsRate(): Percentage { return Percentage::fromString('3'); }
    public function nonCumulativePisRate(): Percentage { return Percentage::fromString('1.65'); }
    public function nonCumulativeCofinsRate(): Percentage { return Percentage::fromString('7.6'); }
}
