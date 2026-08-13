<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator\Domain\Rules;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;

final readonly class InvoiceWithholdingRule implements NormativeRule
{
    public function effectivePeriod(): EffectivePeriod
    {
        return $this->normativeMetadata()->effectivePeriod;
    }

    public function normativeMetadata(): NormativeRuleMetadata
    {
        return new NormativeRuleMetadata(
            identifier: 'invoice_withholding.parametric_2026',
            version: new NormativeRuleVersion('2026.1.0'),
            effectivePeriod: EffectivePeriod::from('2026-01-01', '2026-12-31'),
            references: [
                new NormativeReference(NormativeSourceType::Law, 'Lei 10.833/2003', 'Retenções de CSLL, Cofins e PIS/Pasep em pagamentos por serviços sujeitos às hipóteses legais', ReferenceDate::fromString('2003-12-29'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/2003/l10.833compilado.htm'),
                new NormativeReference(NormativeSourceType::Law, 'Lei 8.212/1991, art. 31', 'Retenção previdenciária nas hipóteses de cessão de mão de obra/empreitada', ReferenceDate::fromString('1991-07-24'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/l8212cons.htm'),
                new NormativeReference(NormativeSourceType::ComplementaryLaw, 'LC 116/2003', 'Regras gerais do ISS, sujeitas à legislação municipal e local da incidência', ReferenceDate::fromString('2003-07-31'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp116.htm'),
                new NormativeReference(NormativeSourceType::Other, 'Receita Federal — IN RFB 1.234/2012', 'Referência para retenções em pagamentos por entes públicos nas hipóteses abrangidas', ReferenceDate::fromString('2012-01-11'), officialUrl: 'https://normas.receita.fazenda.gov.br/sijut2consulta/link.action?idAto=37200'),
            ],
            verifiedAt: ReferenceDate::fromString('2026-08-10'),
            verifiedBy: 'Prazzu Tools',
        );
    }
}
