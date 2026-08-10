<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    /** @param list<array{description:string,merchandise_value:string,mva:string}> $items */
    public function __construct(
        public string $competence,
        public string $operationType,
        public string $originUf,
        public string $destinationUf,
        public string $merchandiseValue,
        public string $freight,
        public string $insurance,
        public string $otherCharges,
        public string $ipi,
        public string $discount,
        public string $originalMva,
        public string $internalRate,
        public string $interstateRate,
        public bool $adjustMva,
        public string $fcpRate,
        public string $ownIcmsOverride,
        public array $items = [],
    ) {}

    public function toArray(): array
    {
        return [
            'competence'=>$this->competence,'operation_type'=>$this->operationType,'origin_uf'=>$this->originUf,'destination_uf'=>$this->destinationUf,
            'merchandise_value'=>$this->merchandiseValue,'freight'=>$this->freight,'insurance'=>$this->insurance,'other_charges'=>$this->otherCharges,
            'ipi'=>$this->ipi,'discount'=>$this->discount,'original_mva'=>$this->originalMva,'internal_rate'=>$this->internalRate,
            'interstate_rate'=>$this->interstateRate,'adjust_mva'=>$this->adjustMva,'fcp_rate'=>$this->fcpRate,'own_icms_override'=>$this->ownIcmsOverride,'items'=>$this->items,
        ];
    }
}
