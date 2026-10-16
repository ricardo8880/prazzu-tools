<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\EmploymentModelComparator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $i): ToolCalculationResult
    {
        if (! $i instanceof CalculationInput) { throw new InvalidArgumentException('Incompatible input.'); }

        $w2Ded = $i->w2Gross->percentage($i->w2EmployeeDeductions);
        $w2Net = $i->w2Gross->subtract($w2Ded)->add($i->w2Benefits);
        $w2Cost = $i->w2Gross->add($i->w2Gross->percentage($i->w2EmployerBurden))->add($i->w2Benefits);

        $contractorTax = $i->contractorRevenue->percentage($i->contractorTaxRate);
        $contractorNet = $i->contractorRevenue->subtract($contractorTax)->subtract($i->contractorExpenses);
        $contractorCost = $i->contractorRevenue;

        $selfTax = $i->selfEmployedRevenue->percentage($i->selfEmployedTaxRate);
        $selfNet = $i->selfEmployedRevenue->subtract($selfTax)->subtract($i->selfEmployedExpenses);
        $selfCost = $i->selfEmployedRevenue;

        $rows = [['W-2 employee',$w2Net,$w2Cost],['1099 contractor',$contractorNet,$contractorCost],['Self-employed',$selfNet,$selfCost]];
        usort($rows, static fn ($a,$b) => $b[1]->minorAmount() <=> $a[1]->minorAmount());

        return new ToolCalculationResult('employee-vs-contractor-calculator', '2.0.0', [
            new ToolCalculationSummaryItem('w2_net', 'W-2 estimated take-home + benefits', $w2Net->formatEnUs(), 'Employer cost: '.$w2Cost->formatEnUs()),
            new ToolCalculationSummaryItem('contractor_net', '1099 estimated net', $contractorNet->formatEnUs(), 'Payer cost: '.$contractorCost->formatEnUs()),
            new ToolCalculationSummaryItem('self_employed_net', 'Self-employed estimated net', $selfNet->formatEnUs(), 'Client revenue: '.$selfCost->formatEnUs()),
            new ToolCalculationSummaryItem('highest_net', 'Highest estimated net', $rows[0][0].' — '.$rows[0][1]->formatEnUs()),
        ], [
            'input' => $i->toArray(),
            'comparison' => [
                'W-2 employee' => ['gross'=>$i->w2Gross->formatEnUs(),'deductions'=>$w2Ded->formatEnUs(),'benefits'=>$i->w2Benefits->formatEnUs(),'net'=>$w2Net->formatEnUs(),'company_cost'=>$w2Cost->formatEnUs()],
                '1099 contractor' => ['gross'=>$i->contractorRevenue->formatEnUs(),'taxes'=>$contractorTax->formatEnUs(),'expenses'=>$i->contractorExpenses->formatEnUs(),'net'=>$contractorNet->formatEnUs(),'company_cost'=>$contractorCost->formatEnUs()],
                'Self-employed' => ['gross'=>$i->selfEmployedRevenue->formatEnUs(),'taxes'=>$selfTax->formatEnUs(),'expenses'=>$i->selfEmployedExpenses->formatEnUs(),'net'=>$selfNet->formatEnUs(),'company_cost'=>$selfCost->formatEnUs()],
            ],
            'assumptions' => ['Tax, deduction, benefit, and employer-burden rates are supplied by the user.', 'This is an economic comparison, not a worker-classification test.'],
        ], warnings: [new ToolCalculationWarning('classification', 'Worker classification depends on the actual relationship and applicable federal/state law. A business cannot make a worker an independent contractor merely by choosing a 1099 model.')]);
    }
}
