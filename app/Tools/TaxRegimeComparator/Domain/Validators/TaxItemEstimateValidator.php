<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Validators;

use App\Core\Exceptions\InvalidValue;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxItemEstimate;

final class TaxItemEstimateValidator
{
    public function validate(TaxItemEstimate $item): void
    {
        if (trim($item->code) === '') {
            throw new InvalidValue('O código do tributo não pode ser vazio.');
        }

        if (trim($item->label) === '') {
            throw new InvalidValue('O nome do tributo não pode ser vazio.');
        }

        if ($item->monthlyAmount->minorAmount() < 0 || $item->annualAmount->minorAmount() < 0) {
            throw new InvalidValue('Os valores estimados do tributo não podem ser negativos.');
        }

        if ($item->monthlyAmount->currency() !== $item->annualAmount->currency()) {
            throw new InvalidValue('Os valores mensal e anual do tributo devem utilizar a mesma moeda.');
        }

        if ($item->effectiveRate !== null && $item->effectiveRate->millionthsOfPercent() < 0) {
            throw new InvalidValue('A alíquota efetiva do tributo não pode ser negativa.');
        }
    }
}
