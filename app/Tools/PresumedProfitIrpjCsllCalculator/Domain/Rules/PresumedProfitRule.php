<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Domain\Rules;

use App\Core\Dates\EffectivePeriod;
use App\Core\Dates\ReferenceDate;
use App\Core\Money\Money;
use App\Core\Normative\Contracts\NormativeRule;
use App\Core\Normative\NormativeReference;
use App\Core\Normative\NormativeRuleMetadata;
use App\Core\Normative\NormativeRuleVersion;
use App\Core\Normative\NormativeSourceType;

final readonly class PresumedProfitRule implements NormativeRule
{
    public function effectivePeriod(): EffectivePeriod
    {
        return $this->normativeMetadata()->effectivePeriod;
    }

    public function normativeMetadata(): NormativeRuleMetadata
    {
        return new NormativeRuleMetadata(
            identifier: 'lucro_presumido.irpj_csll',
            version: new NormativeRuleVersion('2026.1.0'),
            effectivePeriod: EffectivePeriod::from('2026-01-01', '2026-12-31'),
            references: [
                new NormativeReference(NormativeSourceType::Law, 'Lei 9.249/1995', 'Alíquotas e percentuais de presunção do IRPJ', ReferenceDate::fromString('1995-12-26'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/l9249.htm', article: 'arts. 3º e 15'),
                new NormativeReference(NormativeSourceType::Law, 'Lei 9.430/1996', 'Apuração trimestral do lucro presumido', ReferenceDate::fromString('1996-12-27'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/l9430compilada.htm', article: 'arts. 1º, 25 e 26'),
                new NormativeReference(NormativeSourceType::ComplementaryLaw, 'LC 224/2025', 'Redução dos incentivos e benefícios tributários federais', ReferenceDate::fromString('2025-12-26'), officialUrl: 'https://www.planalto.gov.br/ccivil_03/leis/lcp/lcp224.htm', article: 'art. 4º, §§ 4º e 5º'),
                new NormativeReference(NormativeSourceType::Other, 'RFB Benefícios Fiscais V5', 'Perguntas e Respostas — Redução dos Incentivos e Benefícios Tributários', ReferenceDate::fromString('2026-07-30'), officialUrl: 'https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/perguntas-e-respostas/beneficios-fiscais/perguntas-e-respostas-reducao-dos-incentivos-e-beneficios-tributarios-v5-final.pdf', article: 'perguntas 11 a 14'),
                new NormativeReference(NormativeSourceType::Other, 'RFB CSLL', 'Contribuição Social sobre o Lucro Líquido — CSLL', ReferenceDate::fromString('2015-07-10'), officialUrl: 'https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/tributos/CSLL', article: 'alíquotas e lucro presumido'),
            ],
            verifiedAt: ReferenceDate::fromString('2026-08-10'),
            verifiedBy: 'Prazzu Tools',
        );
    }

    /** @return array<string, array{label:string,irpj:string,irpj_increased:string,csll:string,csll_increased:string}> */
    public function activityProfiles(): array
    {
        return [
            'commerce_industry' => ['label' => 'Comércio, indústria, transporte de carga ou serviço hospitalar qualificado', 'irpj' => '8', 'irpj_increased' => '8.8', 'csll' => '12', 'csll_increased' => '13.2'],
            'fuel_resale' => ['label' => 'Revenda de combustíveis abrangida pela presunção de 1,6% no IRPJ', 'irpj' => '1.6', 'irpj_increased' => '1.76', 'csll' => '12', 'csll_increased' => '13.2'],
            'passenger_transport' => ['label' => 'Transporte de passageiros', 'irpj' => '16', 'irpj_increased' => '17.6', 'csll' => '12', 'csll_increased' => '13.2'],
            'services_general' => ['label' => 'Serviços em geral, intermediação, locação/cessão e atividades com presunção de 32%', 'irpj' => '32', 'irpj_increased' => '35.2', 'csll' => '32', 'csll_increased' => '35.2'],
        ];
    }

    public function irpjCumulativeNormalLimit(int $quarter): Money
    {
        return Money::fromDecimal((string) ($quarter * 1_250_000));
    }

    public function csllCumulativeNormalLimit(int $quarter): Money
    {
        if ($quarter <= 1) {
            return Money::zero();
        }

        return Money::fromDecimal((string) (($quarter - 1) * 1_250_000));
    }
}
