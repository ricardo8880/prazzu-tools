<?php

declare(strict_types=1);
namespace App\Tools\TaxReformSimulator\Application\Data;
use App\Core\Tools\Contracts\ToolCalculationInput;
final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public string $price, public string $stateRate, public string $localRate='0', public string $districtRate='0', public int $quantity=1, public bool $taxIncluded=false) {}
    public function toArray(): array { return ['price'=>$this->price,'state_rate'=>$this->stateRate,'local_rate'=>$this->localRate,'district_rate'=>$this->districtRate,'quantity'=>$this->quantity,'tax_included'=>$this->taxIncluded]; }
}
