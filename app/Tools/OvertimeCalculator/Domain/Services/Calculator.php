<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\OvertimeCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(private ?OvertimeCalculator $calculator = null) {}
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (!$input instanceof CalculationInput) throw new InvalidArgumentException('Entrada incompatível com a calculadora de hora extra.');
        $r = ($this->calculator ?? new OvertimeCalculator)->calculate($input->toDomain());
        return new ToolCalculationResult('calculadora-hora-extra', '1.0.0', [
            new ToolCalculationSummaryItem('hourly','Hora normal',$r->hourlyRate->formatPtBr()),
            new ToolCalculationSummaryItem('overtime','Horas extras',$r->overtime50->add($r->overtime100)->add($r->customOvertime)->formatPtBr()),
            new ToolCalculationSummaryItem('night','Adicional noturno',$r->nightPremium->add($r->nightOvertime)->formatPtBr()),
            new ToolCalculationSummaryItem('dsr','DSR',$r->dsr->formatPtBr()),
            new ToolCalculationSummaryItem('total','Total variável',$r->monthlyTotal->formatPtBr()),
        ], [
            'input'=>$input->toArray(),'hourly_minor'=>$r->hourlyRate->minorAmount(),'ot50_minor'=>$r->overtime50->minorAmount(),'ot100_minor'=>$r->overtime100->minorAmount(),'custom_minor'=>$r->customOvertime->minorAmount(),'night_minor'=>$r->nightPremium->minorAmount(),'night_overtime_minor'=>$r->nightOvertime->minorAmount(),'variable_minor'=>$r->variableTotal->minorAmount(),'dsr_minor'=>$r->dsr->minorAmount(),'total_minor'=>$r->monthlyTotal->minorAmount(),'thirteenth_minor'=>$r->thirteenthReflex->minorAmount(),'vacation_minor'=>$r->vacationReflex->minorAmount(),'vacation_third_minor'=>$r->vacationThirdReflex->minorAmount(),'fgts_minor'=>$r->fgtsEstimate->minorAmount(),
        ], warnings: [new ToolCalculationWarning('collective_rules','Confira convenção/acordo coletivo, escala e categoria profissional: percentuais e critérios podem ser mais favoráveis que os mínimos usados na simulação.',ToolCalculationWarningLevel::Info)], calculationMemory: $r->memory);
    }
}
