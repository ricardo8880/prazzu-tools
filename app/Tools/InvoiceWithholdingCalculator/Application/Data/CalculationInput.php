<?php

declare(strict_types=1);

namespace App\Tools\InvoiceWithholdingCalculator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    /** @param list<array{description:string,value:string}> $notes */
    public function __construct(
        public string $competence,
        public string $invoiceNumber,
        public string $serviceDescription,
        public string $grossValue,
        public bool $applyIrrf,
        public string $irrfRate,
        public string $irrfBasePercent,
        public bool $applyInss,
        public string $inssRate,
        public string $inssBasePercent,
        public bool $applyIss,
        public string $issRate,
        public string $issBasePercent,
        public bool $applyPis,
        public string $pisRate,
        public string $pisBasePercent,
        public bool $applyCofins,
        public string $cofinsRate,
        public string $cofinsBasePercent,
        public bool $applyCsll,
        public string $csllRate,
        public string $csllBasePercent,
        public array $notes = [],
    ) {}

    public function toArray(): array
    {
        return [
            'competence'=>$this->competence,'invoice_number'=>$this->invoiceNumber,'service_description'=>$this->serviceDescription,'gross_value'=>$this->grossValue,
            'apply_irrf'=>$this->applyIrrf,'irrf_rate'=>$this->irrfRate,'irrf_base_percent'=>$this->irrfBasePercent,
            'apply_inss'=>$this->applyInss,'inss_rate'=>$this->inssRate,'inss_base_percent'=>$this->inssBasePercent,
            'apply_iss'=>$this->applyIss,'iss_rate'=>$this->issRate,'iss_base_percent'=>$this->issBasePercent,
            'apply_pis'=>$this->applyPis,'pis_rate'=>$this->pisRate,'pis_base_percent'=>$this->pisBasePercent,
            'apply_cofins'=>$this->applyCofins,'cofins_rate'=>$this->cofinsRate,'cofins_base_percent'=>$this->cofinsBasePercent,
            'apply_csll'=>$this->applyCsll,'csll_rate'=>$this->csllRate,'csll_base_percent'=>$this->csllBasePercent,'notes'=>$this->notes,
        ];
    }
}
