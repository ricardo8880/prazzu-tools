<?php

declare(strict_types=1);

namespace App\Tools\PisCofinsCalculator\Application\Data;

use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    /** @param list<array{description:string,revenue:string,credit_base:string}> $operations */
    public function __construct(public string $period, public string $regime, public bool $compareRegimes, public string $taxableRevenue, public string $creditBase, public string $pisWithheld, public string $cofinsWithheld, public array $operations = []) {}

    public function toArray(): array
    {
        return ['period'=>$this->period,'regime'=>$this->regime,'compare_regimes'=>$this->compareRegimes,'taxable_revenue'=>$this->taxableRevenue,'credit_base'=>$this->creditBase,'pis_withheld'=>$this->pisWithheld,'cofins_withheld'=>$this->cofinsWithheld,'operations'=>$this->operations];
    }
}
