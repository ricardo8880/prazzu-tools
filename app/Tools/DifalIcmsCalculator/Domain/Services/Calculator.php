<?php

declare(strict_types=1);

namespace App\Tools\DifalIcmsCalculator\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\DifalIcmsCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(private ?DifalCalculator $calculator = null) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a calculadora DIFAL.');
        } $r = ($this->calculator ?? new DifalCalculator)->calculate($input->toDomain());

        return new ToolCalculationResult('calculadora-difal-icms', '1.0.0', [new ToolCalculationSummaryItem('interstate', 'Alíquota interestadual', $r->interstateRate->toDecimalString().'%'), new ToolCalculationSummaryItem('destination_base', 'Base no destino', $r->destinationBase->formatPtBr()), new ToolCalculationSummaryItem('difal', 'DIFAL', $r->difal->formatPtBr()), new ToolCalculationSummaryItem('fcp', 'FCP', $r->fcp->formatPtBr()), new ToolCalculationSummaryItem('total', 'Total destino', $r->totalDestination->formatPtBr())], ['input' => $input->toArray(), 'origin_icms_minor' => $r->originIcms->minorAmount(), 'destination_base_minor' => $r->destinationBase->minorAmount(), 'destination_icms_minor' => $r->destinationIcms->minorAmount(), 'difal_minor' => $r->difal->minorAmount(), 'fcp_minor' => $r->fcp->minorAmount(), 'total_minor' => $r->totalDestination->minorAmount(), 'interstate_rate' => $r->interstateRate->toDecimalString()], warnings: [new ToolCalculationWarning('state_rules', 'Confirme alíquota interna, FCP, benefícios, NCM e método de base na legislação da UF de destino antes do recolhimento.', ToolCalculationWarningLevel::Info), new ToolCalculationWarning('responsibility', $input->recipientTaxpayer ? 'Destinatário contribuinte: em regra, a responsabilidade pelo diferencial é do destinatário, observadas as normas do caso.' : 'Destinatário não contribuinte: em regra, a responsabilidade pelo diferencial é do remetente/prestador, observadas as normas do caso.', ToolCalculationWarningLevel::Info)], calculationMemory: $r->memory);
    }
}
