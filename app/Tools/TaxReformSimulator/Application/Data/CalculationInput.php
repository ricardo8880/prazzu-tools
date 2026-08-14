<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Application\Data; use App\Core\Tools\Contracts\ToolCalculationInput; final readonly class CalculationInput implements ToolCalculationInput { public function __construct(public string $revenue,public string $legacyFederalRate,public string $legacySubnationalRate,public string $cbsReferenceRate,public string $ibsReferenceRate,public string $creditBasePercent='0',public int $year=2026){} public function toArray():array{return ['revenue'=>$this->revenue,'legacy_federal_rate'=>$this->legacyFederalRate,'legacy_subnational_rate'=>$this->legacySubnationalRate,'cbs_reference_rate'=>$this->cbsReferenceRate,'ibs_reference_rate'=>$this->ibsReferenceRate,'credit_base_percent'=>$this->creditBasePercent,'year'=>$this->year];} }
