<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator\Domain\Services;

use App\Core\Documents\Data\GeneratedDocumentNotice;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\IncomeStatementGenerator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if(!$input instanceof CalculationInput){throw new InvalidArgumentException('Incompatible input.');}
        $deductions=$input->federalIncomeTax->add($input->socialSecurity)->add($input->medicare)->add($input->stateLocalTax)->add($input->otherDeductions);
        if($deductions->minorAmount()>$input->gross->minorAmount()){throw new InvalidArgumentException('Deductions cannot exceed gross earnings.');}
        $net=$input->gross->subtract($deductions);
        $notice=new GeneratedDocumentNotice(
            purpose:'Summarize earnings and withholding amounts supplied by the user.',
            limitations:['This is not Form W-2, Form 1099, a payroll filing, or an IRS/state tax document.','The tool does not verify identity, employer records, withholding accuracy, or filing obligations.','Use official employer/payroll/tax documents when an official statement is required.']
        );
        return new ToolCalculationResult('earnings-statement-generator','2.0.0',[
            new ToolCalculationSummaryItem('gross','Gross earnings',$input->gross->formatEnUs()),
            new ToolCalculationSummaryItem('deductions','Reported withholding/deductions',$deductions->formatEnUs()),
            new ToolCalculationSummaryItem('net','Net earnings',$net->formatEnUs()),
        ],[
            'input'=>$input->toArray(),
            'document'=>[
                'title'=>'Annual Earnings Statement','payer'=>$input->payer,'beneficiary'=>$input->name,'employee_id'=>$input->employeeId,'year'=>$input->year,
                'gross'=>$input->gross->formatEnUs(),'federal_income_tax'=>$input->federalIncomeTax->formatEnUs(),'social_security'=>$input->socialSecurity->formatEnUs(),'medicare'=>$input->medicare->formatEnUs(),'state_local_tax'=>$input->stateLocalTax->formatEnUs(),'other'=>$input->otherDeductions->formatEnUs(),'net'=>$net->formatEnUs(),
                'declaration'=>"{$input->payer} reports that {$input->name} (employee/reference ID {$input->employeeId}) received the amounts shown in this statement for calendar year {$input->year}.",
                'notice'=>$notice->toArray(),
            ],
        ],warnings:[new ToolCalculationWarning('not_official_tax_form','This generated statement is informational and is not a substitute for an official W-2, 1099, payroll record, or tax filing.')],calculationMemory:new CalculationMemory('2.0.0',[
            new CalculationMemoryStep('deductions','Total reported withholding and deductions','federal income tax + Social Security + Medicare + state/local tax + other deductions',[],$deductions->minorAmount()),
            new CalculationMemoryStep('net','Net earnings','gross earnings - reported withholding/deductions',[],$net->minorAmount()),
        ],assumptions:['All figures are supplied by the user and are not independently verified.'])) ;
    }
}
