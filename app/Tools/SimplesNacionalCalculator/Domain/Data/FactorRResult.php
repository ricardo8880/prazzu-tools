<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;

final readonly class FactorRResult
{
    public function __construct(
        public Money $payroll12,
        public Money $rbt12,
        public Percentage $factorR,
        public Percentage $threshold,
        public TaxAnnex $applicableAnnex,
        public string $ruleVersion,
        public string $ruleValidFrom,
    ) {}

    public function qualifiesForAnnexIII(): bool
    {
        return $this->applicableAnnex === TaxAnnex::III;
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'payroll_12' => $this->payroll12->formatPtBr(),
            'rbt12' => $this->rbt12->formatPtBr(),
            'factor_r' => $this->factorR->toDecimalString().'%',
            'threshold' => $this->threshold->toDecimalString().'%',
            'applicable_annex' => $this->applicableAnnex->value,
            'applicable_annex_label' => $this->applicableAnnex->label(),
            'formula' => 'Folha de salários dos últimos 12 meses ÷ receita bruta dos últimos 12 meses',
            'explanation' => $this->qualifiesForAnnexIII()
                ? 'O Fator R é igual ou superior a 28%; aplica-se o Anexo III às atividades sujeitas a esta regra.'
                : 'O Fator R é inferior a 28%; aplica-se o Anexo V às atividades sujeitas a esta regra.',
            'rule_version' => $this->ruleVersion,
            'rule_valid_from' => $this->ruleValidFrom,
        ];
    }
}
