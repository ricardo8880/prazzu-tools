<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Validators;

use App\Core\Exceptions\InvalidValue;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxRegimeEstimate;
use App\Tools\TaxRegimeComparator\Domain\Enums\EstimateStatus;

final class TaxRegimeEstimateValidator
{
    public function validate(TaxRegimeEstimate $estimate): void
    {
        if ($estimate->status === EstimateStatus::Available) {
            if ($estimate->estimatedMonthlyTax === null || $estimate->estimatedAnnualTax === null) {
                throw new InvalidValue('Uma estimativa disponível deve informar os totais mensal e anual.');
            }
        } elseif ($estimate->estimatedMonthlyTax !== null || $estimate->estimatedAnnualTax !== null) {
            throw new InvalidValue('Uma estimativa indisponível não pode informar totais tributários.');
        }

        if ($estimate->estimatedMonthlyTax !== null && $estimate->estimatedMonthlyTax->minorAmount() < 0) {
            throw new InvalidValue('O total tributário mensal não pode ser negativo.');
        }

        if ($estimate->estimatedAnnualTax !== null && $estimate->estimatedAnnualTax->minorAmount() < 0) {
            throw new InvalidValue('O total tributário anual não pode ser negativo.');
        }

        if ($estimate->estimatedMonthlyTax !== null
            && $estimate->estimatedAnnualTax !== null
            && $estimate->estimatedMonthlyTax->currency() !== $estimate->estimatedAnnualTax->currency()) {
            throw new InvalidValue('Os totais tributários mensal e anual devem utilizar a mesma moeda.');
        }

        foreach ($estimate->taxes as $tax) {
            if ($estimate->estimatedMonthlyTax !== null
                && $tax->monthlyAmount->currency() !== $estimate->estimatedMonthlyTax->currency()) {
                throw new InvalidValue('Todos os tributos devem utilizar a moeda dos totais do regime.');
            }
        }
    }
}
