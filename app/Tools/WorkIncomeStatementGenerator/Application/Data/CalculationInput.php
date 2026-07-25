<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public string $name, public string $document, public string $employer, public string $occupation, public string $startDate, public Money $monthlyIncome, public string $city, public string $issueDate) {}

    public function toArray(): array
    {
        return ['name' => $this->name, 'document' => $this->document, 'employer' => $this->employer, 'occupation' => $this->occupation, 'start_date' => $this->startDate, 'monthly_income' => $this->monthlyIncome->minorAmount(), 'city' => $this->city, 'issue_date' => $this->issueDate];
    }
}
