<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator\Application\Data;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(public Money $cltGross, public Money $cltBenefits, public Percentage $cltEmployeeDeductions, public Percentage $cltCompanyBurden, public Money $pjInvoice, public Percentage $pjTaxes, public Money $pjExpenses, public Money $autonomousGross, public Percentage $autonomousDeductions, public Percentage $autonomousCompanyBurden) {}

    public function toArray(): array
    {
        return ['clt_gross' => $this->cltGross->minorAmount(), 'clt_benefits' => $this->cltBenefits->minorAmount(), 'clt_employee_deductions' => $this->cltEmployeeDeductions->toDecimalString(), 'clt_company_burden' => $this->cltCompanyBurden->toDecimalString(), 'pj_invoice' => $this->pjInvoice->minorAmount(), 'pj_taxes' => $this->pjTaxes->toDecimalString(), 'pj_expenses' => $this->pjExpenses->minorAmount(), 'autonomous_gross' => $this->autonomousGross->minorAmount(), 'autonomous_deductions' => $this->autonomousDeductions->toDecimalString(), 'autonomous_company_burden' => $this->autonomousCompanyBurden->toDecimalString()];
    }
}
