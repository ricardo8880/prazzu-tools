<?php

declare(strict_types=1);

namespace App\Tools\EmploymentModelComparator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\EmploymentModelComparator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $i): ToolCalculationResult
    {
        if (! $i instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }
        $cltDed = $i->cltGross->percentage($i->cltEmployeeDeductions);
        $cltNet = $i->cltGross->subtract($cltDed)->add($i->cltBenefits);
        $cltCost = $i->cltGross->add($i->cltGross->percentage($i->cltCompanyBurden))->add($i->cltBenefits);
        $pjTax = $i->pjInvoice->percentage($i->pjTaxes);
        $pjNet = $i->pjInvoice->subtract($pjTax)->subtract($i->pjExpenses);
        $pjCost = $i->pjInvoice;
        $autoDed = $i->autonomousGross->percentage($i->autonomousDeductions);
        $autoNet = $i->autonomousGross->subtract($autoDed);
        $autoCost = $i->autonomousGross->add($i->autonomousGross->percentage($i->autonomousCompanyBurden));
        $rows = [['CLT', $cltNet, $cltCost], ['PJ', $pjNet, $pjCost], ['Autônomo', $autoNet, $autoCost]];
        usort($rows, fn ($a, $b) => $b[1]->minorAmount() <=> $a[1]->minorAmount());

        return new ToolCalculationResult('comparador-clt-pj-autonomo', '1.0.0', [
            new ToolCalculationSummaryItem('clt_net', 'Líquido CLT', $cltNet->formatPtBr(), 'Custo empresa: '.$cltCost->formatPtBr()),
            new ToolCalculationSummaryItem('pj_net', 'Líquido PJ', $pjNet->formatPtBr(), 'Custo empresa: '.$pjCost->formatPtBr()),
            new ToolCalculationSummaryItem('autonomous_net', 'Líquido autônomo', $autoNet->formatPtBr(), 'Custo empresa: '.$autoCost->formatPtBr()),
            new ToolCalculationSummaryItem('highest_net', 'Maior líquido estimado', $rows[0][0].' — '.$rows[0][1]->formatPtBr()),
        ], ['input' => $i->toArray(), 'comparison' => [
            'CLT' => ['gross' => $i->cltGross->formatPtBr(), 'deductions' => $cltDed->formatPtBr(), 'benefits' => $i->cltBenefits->formatPtBr(), 'net' => $cltNet->formatPtBr(), 'company_cost' => $cltCost->formatPtBr()],
            'PJ' => ['gross' => $i->pjInvoice->formatPtBr(), 'taxes' => $pjTax->formatPtBr(), 'expenses' => $i->pjExpenses->formatPtBr(), 'net' => $pjNet->formatPtBr(), 'company_cost' => $pjCost->formatPtBr()],
            'Autônomo' => ['gross' => $i->autonomousGross->formatPtBr(), 'deductions' => $autoDed->formatPtBr(), 'net' => $autoNet->formatPtBr(), 'company_cost' => $autoCost->formatPtBr()],
        ]]);
    }
}
