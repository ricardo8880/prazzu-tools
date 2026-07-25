<?php

declare(strict_types=1);

namespace App\Tools\LateDasCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;
use DateTimeImmutable;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public Money $principal, public DateTimeImmutable $dueDate, public DateTimeImmutable $paymentDate, public Percentage $accumulatedSelic) {}

    public function toArray(): array
    {
        return ['principal' => $this->principal->minorAmount(), 'due_date' => $this->dueDate->format('Y-m-d'), 'payment_date' => $this->paymentDate->format('Y-m-d'), 'accumulated_selic' => $this->accumulatedSelic->toDecimalString()];
    }
}
