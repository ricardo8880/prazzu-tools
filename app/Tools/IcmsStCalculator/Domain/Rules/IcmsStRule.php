<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator\Domain\Rules;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;

final readonly class IcmsStRule implements NormativeRule
{
    public function effectivePeriod(): EffectivePeriod
    {
        return $this->normativeMetadata()->effectivePeriod;
    }

    public function normativeMetadata(): NormativeRuleMetadata
    {
        return new NormativeRuleMetadata(
            identifier: 'icms_st.parametric_2026',
            version: new NormativeRuleVersion('2026.1.0'),
            effectivePeriod: EffectivePeriod::from('2026-01-01', '2026-12-31'),
            references: [
                new NormativeReference(NormativeSourceType::Other, 'Convênio ICMS 142/2018', 'Normas gerais de substituição tributária e antecipação de ICMS', ReferenceDate::fromString('2018-12-14'), officialUrl: 'https://www.confaz.fazenda.gov.br/legislacao/convenios/2018/CV142_18'),
                new NormativeReference(NormativeSourceType::Other, 'SEFAZ-PE — Fórmula da MVA Ajustada', 'Referência oficial para a fórmula de ajuste da MVA em operação interestadual', ReferenceDate::fromString('2026-08-10'), officialUrl: 'https://www.sefaz.pe.gov.br/Servicos/Substituicao-Tributaria/Paginas/Formula-da-MVA-Ajustada.aspx'),
                new NormativeReference(NormativeSourceType::Other, 'Receita Estadual RS — Cálculo do ICMS-ST', 'Orientação oficial sobre composição e apuração do ICMS-ST', ReferenceDate::fromString('2026-08-10'), officialUrl: 'https://atendimento.receita.rs.gov.br/calculo-do-icms-st'),
            ],
            verifiedAt: ReferenceDate::fromString('2026-08-10'),
            verifiedBy: 'Prazzu Tools',
        );
    }
}
