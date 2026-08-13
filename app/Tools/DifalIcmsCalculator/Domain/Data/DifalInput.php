<?php

declare(strict_types=1);

namespace App\Tools\DifalIcmsCalculator\Domain\Data;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Core\Money\Percentage;

final readonly class DifalInput
{
    public function __construct(public Competence $competence, public Money $base, public string $originUf, public string $destinationUf, public bool $imported, public ?Percentage $interstateOverride, public Percentage $internalRate, public Percentage $fcpRate, public string $method, public bool $recipientTaxpayer) {}
}
