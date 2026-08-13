<?php

declare(strict_types=1);

namespace App\Tools\DifalIcmsCalculator\Application\Data;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\DifalIcmsCalculator\Domain\Data\DifalInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public string $competence, public string $base, public string $originUf, public string $destinationUf, public bool $imported = false, public ?string $interstateRate = null, public string $internalRate = '18', public string $fcpRate = '0', public string $method = 'single_base', public bool $recipientTaxpayer = false) {}

    public function toDomain(): DifalInput
    {
        return new DifalInput(Competence::fromString($this->competence), Money::fromDecimal($this->base), strtoupper($this->originUf), strtoupper($this->destinationUf), $this->imported, $this->interstateRate !== null && $this->interstateRate !== '' ? Percentage::fromString($this->interstateRate) : null, Percentage::fromString($this->internalRate), Percentage::fromString($this->fcpRate), $this->method, $this->recipientTaxpayer);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
